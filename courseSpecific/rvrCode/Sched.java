import java.util.*;
import java.io.*;
import au.com.bytecode.opencsv.CSVReader;

abstract class XML {
	abstract public String toString(String indent);

	public String toString(){
		return toString("");
	}
};

class NodeXML extends XML {
	String tag;
	Map<String, String> attrs = new HashMap<String, String>();
	Vector<XML> children = new Vector<XML>();

	NodeXML(String tag){
		this.tag = tag;
	}

	void add(XML child){
		children.add(child);
	}

	public String toString(String indent){
		boolean endsWithText = false;
		String result = indent + "<" + tag + ">";
		for (XML xml : children) {
			if (xml instanceof TextXML) {
				result += xml.toString();
				endsWithText = true;
			}
			else {
				result += "\n" + xml.toString(indent + "   ");
				endsWithText = false;
			}
		}
		if (!endsWithText) {
			result += "\n" + indent;
		}
		return result + "</" + tag + ">";
	}
};

class TextXML extends XML {
	String text;

	TextXML(String text){
		this.text = text;
	}

	public String toString(String indent){
		return text;
	}
};

/* All the information for bands that we need.
 */
class Band {
	String name;			// say, "The Amazing Crooners"
	String address;			// say, "100 Franklin St"
	String blurb;			// say, "barbershop quartet"
	double lat;				// say, 42.450962
	double lng;				// say, -76.501122
	boolean[] timeslots;	// say, [ true, true, false, true ]
	String[] conflicts;		// say, [ "The Fantastic Yelpers" ]
	int slot;				// timeslot to be assigned

	Band(final String[] slots, String name, String address, String blurb, String lat, String lng, String candos, String conflicts, Random rnd, int[] slotsize){
//		System.out.println(">> '" + name + "' " + conflicts);
		this.name = name;
		this.address = address;
		this.blurb = blurb;
		this.lat = Double.parseDouble(lat);
		this.lng = Double.parseDouble(lng);
		this.conflicts = conflicts.length() == 0 ? new String[0] : conflicts.split(", *");
		for (int i = 0; i < this.conflicts.length; i++) {
			this.conflicts[i] = this.conflicts[i].trim();
		}

		/* Figure out which slots this band can play in (ignoring
		 * conflicts with other bands.
		 */
		timeslots = new boolean[slots.length];
		for (int i = 0; i < slots.length; i++) {
			timeslots[i] = false;
		}
		if (candos.length() > 0) {
			String[] parts = candos.split(", *");
			for (String cando : parts) {
				if (!cando.startsWith("Anytime")) {
					boolean found = false;
					for (int i = 0; i < slots.length; i++) {
						if (cando.equals(slots[i])) {
							timeslots[i] = true;
							found = true;
							break;
						}
					}
					if (!found) {
						System.err.println("Couldn't find conflict '" + cando +
									"' in band '" + name + "'");
					}
				}
			}
		}

		assignToSlot(rnd, slotsize);
	}

	String dumpTimes(){
		String result = "";
		for (boolean b : timeslots) {
			result += b ? "1" : "0";
		}
		return result;
	}

	/* Assign to slot at random, preferring slots with few bands over
	 * slots with many bands.
	 */
	void assignToSlot(Random rnd, int[] slotsize){
		/* Calculate total "weight".
		 */
		double total = 0;
		for (int i = 0; i < timeslots.length; i++) {
			if (timeslots[i]) {
				total += 1.0 / (slotsize[i] + 1);
			}
		}
		if (total <= 0) {
			System.err.println("No slots possible for band " + name);
			System.exit(1);
		}

		/* Pick a random weight.
		 */
		double weight = rnd.nextDouble() * total;
		for (int i = 0; i < timeslots.length; i++) {
			if (timeslots[i]) {
				weight -= 1.0 / (slotsize[i] + 1);
				if (weight <= 0) {
					slot = i;
					break;
				}
			}
		}
		slotsize[slot]++;
	}

	XML toXML(){
		NodeXML root = new NodeXML("Placemark");
		NodeXML styleURL = new NodeXML("styleURL");
		styleURL.add(new TextXML("#icon-503-DB4436"));
		root.add(styleURL);
		NodeXML nameNode = new NodeXML("name");
		nameNode.add(new TextXML("<![CDATA[" + name + " - " + address + "]]>"));
		root.add(nameNode);
		root.add(new NodeXML("ExtendedData"));
		NodeXML blurbNode = new NodeXML("description");
		blurbNode.add(new TextXML("<![CDATA[" + blurb + "]]>"));
		root.add(blurbNode);
		NodeXML coords = new NodeXML("coordinates");
		coords.add(new TextXML("" + lng + "," + lat));
		NodeXML points = new NodeXML("Point");
		points.add(coords);
		root.add(points);
		return root;
	}

	/* Return the distance in metres between two locations.
	 */
	double dist(Band b) {
		/* For some reason it's having trouble with this case.
		 */
		if (lat == b.lat && lng == b.lng) {
			return 0;
		}

		double pk = 180/3.14169;
		double a1 = lat / pk;
		double a2 = lng / pk;
		double b1 = b.lat / pk;
		double b2 = b.lng / pk;
		double t1 = Math.cos(a1)*Math.cos(a2)*Math.cos(b1)*Math.cos(b2);
		double t2 = Math.cos(a1)*Math.sin(a2)*Math.cos(b1)*Math.sin(b2);
		double t3 = Math.sin(a1)*Math.sin(b1);
		Double d = 6366000 * Math.acos(t1 + t2 + t3);
		if (d.isNaN()) {
			System.err.printf("dist %f %f %f %f\n", lat, lng, b.lat, b.lng);
			System.exit(1);
		}
		return d;
	}

	/* Return if band "name" conflicts with this band.
	 */
	boolean conflictsWith(String name){
		for (String c : conflicts) {
			if (c.equalsIgnoreCase(name)) {
				return true;
			}
		}
		return false;
	}

	/* Compute 'tdistance' between this band (at a particular time slot)
	 * and another.  Higher is better.  This is:
	 *
	 *	infinity if the bands are in different time slots
	 *	0 if the bands are in the same time slot and conflict with one another
	 *	distance in metres (given as argument) if the bands are in the same time slot and don't conflict with one another
	 */
	int tdistance(Band b, int dist){
		if (b.slot != slot) {
			return 9999;		// infinity...  (Integer.MAX_VALUE would work)
		}
		if (conflictsWith(b.name) || b.conflictsWith(name)) {
			return 0;
		}
		return dist;
	}

	public String toString(){
		return toXML().toString();
	}
}

public class Sched {
	static final int[] order = {
		0,			// name
		1,			// latitude
		2,			// longitude
		3,			// address
		4,			// blurb
		5,			// candos
		6,			// conflicts
	};

	static final String[] slots = {
		"12-1pm", "1-2pm", "2-3pm", "3-4pm", "4-5pm", "5-6pm"
	};

	Vector<Band> bands = new Vector<Band>();	// list of bands
	int [][] dist;								// distances between bands
	int [] slotsize;							// # bands in slot
	Random rnd = new Random();					// random number generator

	int besttdist = 0;
	double avgdist, avgspread;

	/* Print the 'tdistances' between any two bands.
	 */
	void dumpTdist(){
		for (int i = 0; i < bands.size(); i++) {
			Band bi = bands.get(i);
			for (int j = 0; j < i; j++) {
				Band bj = bands.get(j);
				System.out.print("," + bi.tdistance(bj, dist[i][j]));
			}
			System.out.println();
		}
	}

	void dumpKML(String out, int timeslot){
		try {
			PrintStream ps = new PrintStream(out);

			ps.println("<?xml version='1.0' encoding='UTF-8'?>");
			ps.println("<kml xmlns='http://www.opengis.net/kml/2.2'>");
			NodeXML doc = new NodeXML("Document");
			NodeXML nameNode = new NodeXML("name");
			nameNode.add(new TextXML("bands"));
			doc.add(nameNode);
			for (Band b: bands) {
				if (b.slot == timeslot) {
					doc.add(b.toXML());
				}
			}
			ps.print(doc.toString("    "));
			ps.println("</kml>");
		}
		catch (Exception e) {
			System.err.println("Output error: " + e);
			System.exit(1);
		}
	}

	/* Parse the input and put it in the vector 'bands'.  Also initialize
	 * matrix dist[][] with distances between bands in meters.
	 */
	void parse(String file){
		slotsize = new int[slots.length];
		for (int i = 0; i < slots.length; i++) {
			slotsize[i] = 0;
		}

		try {
			CSVReader reader = new CSVReader(new FileReader(file));
			List<String[]> input = reader.readAll();
			for (String[] line : input) {
				if (line[order[0]].length() == 0) {
					continue;
				}
				if (line.length < 6) {
					System.out.println("Error in >> " + line[order[0]]);
				}
				String conflicts = line.length == 6 ? "" : line[order[6]];
				Band b = new Band(slots,
							line[order[0]],
							line[order[3]],
							line[order[4]],
							line[order[1]],
							line[order[2]],
							line[order[5]],
							conflicts,
							rnd, slotsize);
				bands.add(b);
			}
		}
		catch (IOException e) {
			System.out.println("Parsing error: " + e);
			System.exit(1);
		}

		/* Compute distances.  Really only need to fill half of this matrix
		 * but it's convenient to just fill it.
		 */
		dist = new int[bands.size()][bands.size()];
		for (int i = 0; i < bands.size(); i++) {
			Band bi = bands.get(i);
			for (int j = 0; j < bands.size(); j++) {
				Band bj = bands.get(j);
				dist[i][j] = (int) bi.dist(bj);
			}
		}
	}

	/* Check band conflicts and see if it all makes sense...
	 */
	void dumpWeirdConflicts(){
		/* Create a set of band names.  Check for duplicates while at it.
		 */
		Set<String> names = new HashSet<String>();
		for (Band b: bands) {
			String name = b.name.toLowerCase();
			if (names.contains(name)) {
				System.out.printf("Band '%s' appears multiple times\n", b.name);
			}
			names.add(name);
		}

		/* Now see which conflicts we don't understand.
		 */
		for (Band b: bands) {
			boolean first = true;
			for (String c : b.conflicts) {
				if (!names.contains(c.toLowerCase())) {
					if (first) {
						System.out.printf("\nBand '%s' conflicts with unknown: %s", b.name, c);
						first = false;
					}
					else {
						System.out.printf(", %s", c);
					}
				}
			}
			if (!first) {
				System.out.println();
			}
		}
	}

	void dump(){
		for (int t = 0; t < slots.length; t++) {
			dumpKML("slot" + (t+1) + ".kml", t);
			System.out.printf("Slot %s: %d bands\n", slots[t], slotsize[t]);
		}
		System.out.printf("maximum min. distance: %d\n", besttdist);
		System.out.printf("minimum avg. distance: %f\n", avgdist); 
		System.out.printf("minimum avg. spread: %f\n", avgspread); 
	}

	public void run(String file){
		parse(file);

		int noimprovements = 0;
		for (;;) {
			/* Find two bands that are closest.  If multiple select two
			 * bands at random.
			 */
			Band bx = null, by = null;
			int count = 0, maxtdist = 9999 + 1;
			for (int i = 0; i < bands.size(); i++) {
				Band bi = bands.get(i);
				for (int j = 0; j < i; j++) {
					Band bj = bands.get(j);
					int tdist = bi.tdistance(bj, dist[i][j]);
					if (tdist < maxtdist) {
						maxtdist = tdist;
						bx = bi;
						by = bj;
						count = 0;
					}
					else if (tdist == maxtdist) {
						count++;
						if (rnd.nextInt(count) == 0) {
							bx = bi;
							by = bj;
						}
					}
				}
			}

			if (maxtdist > besttdist) {
				besttdist = maxtdist;
			}

			/* Assign one (at random) to a new slot.
			 */
			Band br = rnd.nextInt(2) == 0 ? bx : by;
			int oldslot = br.slot;
			slotsize[oldslot]--;
			br.assignToSlot(rnd, slotsize);

			/* See if this improves the minimum distance. 
			 */
			int max2 = 9999 + 1;
			for (int i = 0; i < bands.size(); i++) {
				Band bi = bands.get(i);
				if (bi != br) {
					int tdist = bi.tdistance(br, (int) bi.dist(br));
					if (tdist < max2) {
						max2 = tdist;
					}
				}
			}

			/* If not, restore slot.
			 */
			if (max2 < maxtdist) {
				slotsize[br.slot]--;
				br.slot = oldslot;
				slotsize[br.slot]++;
			}

			if (max2 > maxtdist) {
				noimprovements = 0;
			}

			if (max2 <= maxtdist) {
				if (++noimprovements == 100) {
					break;
				}
			}
		}

		/* Compute the average distance between bands.
		 */

		// dumpTdist();

		/* Compute the avg. distance in each slot and save the minimum.
		 */
		avgdist = 1000000;
		for (int t = 0; t < slots.length; t++) {
			if (slotsize[t] == 0) {
				System.err.println("No bands in slot " + t);
				System.exit(1);
			}
			double total = 0;
			for (int i = 0; i < bands.size(); i++) {
				Band bi = bands.get(i);
				if (bi.slot != t) {
					continue;
				}
				for (int j = 0; j < bands.size(); j++) {
					Band bj = bands.get(j);
					if (bj.slot != t) {
						continue;
					}
					total += bi.dist(bj);
				}
			}
			double avg = (double) total / slotsize[t] / slotsize[t];
			if (avg < avgdist) {
				avgdist = avg;
			}
		}

		avgspread = 10000000;
		for (int t = 0; t < slots.length; t++) {
			double total = 0;
			for (int i = 0; i < bands.size(); i++) {
				Band bi = bands.get(i);
				if (bi.slot != t) {
					continue;
				}

				/* Find the distance to the nearest-by band.
				 */
				int min = 100000;
				for (int j = 0; j < bands.size(); j++) {
					if (j == i) {
						continue;
					}
					Band bj = bands.get(j);
					if (bj.slot != t) {
						continue;
					}
					if (dist[i][j] < min) {
						min = dist[i][j];
					}
				}
				total += 1.0 / min;
			}
			double avg = slotsize[t] / total;
			if (avg < avgspread) {
				avgspread = avg;
			}
		}
	}

	public static void main(String[] args){
		String file = args.length > 0 ? args[0] : "Porchfest_signups.csv";
		Set<Sched> set = new HashSet<Sched>();

		for (int i = 0; i < 100; i++) {
			System.err.printf("Attempt %d\n", i);
			Sched s = new Sched();
			s.run(file);
			set.add(s);
		}

		/* Find the best one.
		 */
		Sched best = null;
		for (Sched s : set) {
			if (best == null || s.besttdist > best.besttdist) {
				best = s;
			}
			if (s.besttdist == best.besttdist && s.avgspread > best.avgspread) {
				best = s;
			}
		}

		best.dumpWeirdConflicts();
		best.dump();
	}
}
