# future ideas:
# order by conflicts, or by location clusters. Add this in later
# explicitly check if the variances between timeslots are roughly equivalent
  # In the section where we randomly swap between the worst time slot and any other, it might make sense to swap with the best one instead

#******* PREPROCESS *******

class Band:
  int ID;
  String name;      # say, "The Amazing Crooners"
  float lat;        # say, 42.450962
  float lng;        # say, -76.501122
  boolean[] availableTimeSlots; # say, [ true, true, false, true ]
  int[] conflicts;    # say, [ 11111 ] band IDs that we conflict with
  int slot;         # initially -1 until assigned
  HashMap<int bandID, int d> distances
  
  Band(int ID, float lat, float long, String name, int[] conflicts, bool[] availableTimeSlots)
  
  def getDistance(int bandID):
    if (!distances.containsKey(bandID)):
      Band b = bandsHashMap.get(bandID)
      distances.add(bandID, tdistance(self.lat, self.lng, b.lat, b.lng))
    return distances.get(bandID)

  def calculateKNearest(int k, Schedule sched):
    Band[] bands = sched.getBandsAtSlot(slot)
    sortByDistance(bands)
    int[] result = []
    for (int i = 0; i < k; i++): # get the nearest k IDs
      result.push(bands[i].ID)
    return result
  
  def sortByDistance(Band b):
    #TODO
  
class Schedule:
  HashMap<int timeslot, Band[] bands> schedule
  
  float[] timeSlotVariances
  
  add(int slot, Band band)
  
  swap()
  
  delete()
  
  export()
  
  Band[] getBandsAtSlot(int timeSlot)
  
# pull data from DB
# for each band we'll need: available time slots, address, conflicts
bandsTimeSlots = DB.getBandsTimeSlots() #for available time slots
bandsPorchfests = DB.getBandsPorchfests() #for bands in this porchfest
timeslotsPorchfests = DB.getTimeslotsPorchfests() #to see all timeslots available for a porchfest
bands = DB.getBands() #for porch location and conflicts

# create band objects and parse lat/long while we're at it - google geocode stuff <https://developers.google.com/maps/documentation/geocoding/intro>
HashMap<int id, Band band> bandsHashMap = new HashMap<int id, Band band>()
HashMap<int numberOfTimeSlots, int[] bandIds> bandsWithXTimeSlots = new HashMap<int numTimeSlots, int[] bandIds>() #max number of time slots a band can play in

for (band in bandsPorchfests):
  int id = band.getID()
  bool[] availableTimeSlots = parse(bandsTimeSlots.getTimeSlot(id))
  float[] latLong = googleAPI.get(bands.getAddress(id))
  int[] conflicts = bands.getConflicts(id) # UPDATE SCHEMA
  bandsHashMap.add(id, new Band(id, latLong.lat, latLong.long, band.getName(id), conflicts, availableTimeSlots))

# populate the numberOfTimeSlots hashmap
def populateBandsWithXTimeSlots():
  int maxNumberOfTimeSlots = timeslotsPorchfests.size()
  for (i = 1; i <= maxNumberOfTimeSlots; i++):
    bandsWithXTimeSlots.add(i, [])
  
  for (band in bandsPorchfests):
    int availTimeSlots = bandsTimeSlots.getTimeSlot(id).size() # assume getTimeSlots returns an array of timeslots
    bandsWithXTimeSlots[availTimeSlots].push(band.getID())

#******* ALGORITHM *******

# HELPER FUNCTIONS

# moves band to a different timeslot
# returns true on success, false otherwise
def tryToMoveBand(int id):
  band = bandsHashMap.get(id)
  for (slot in totalTimeSlots):
    if (slot == band.slot):
      continue
    if ( band.availableTimeSlots[slot] && noConflicts(schedule.get(slot), band) ):
        schedule.delete(band.slot, band)
        schedule.add(slot, band)
        band.slot = slot
        return True
  return False

def noConflicts(Band[] bands, Band band):
  for (conflict in bands.getConflicts()):
    if (bands.contains(conflict)):
      return False
  return True

def tdistance(lat1, lng1, lat2, lng2):
  # calculate distance between self and b
  # For some reason it's having trouble with this case.
  if (lat1 == lat2 && lng1 == lng2):
    return 0;

  double pk = 180/3.14169;
  double a1 = lat1 / pk;
  double a2 = lng1 / pk;
  double b1 = lat2 / pk;
  double b2 = lng2 / pk;
  double t1 = Math.cos(a1)*Math.cos(a2)*Math.cos(b1)*Math.cos(b2);
  double t2 = Math.cos(a1)*Math.sin(a2)*Math.cos(b1)*Math.sin(b2);
  double t3 = Math.sin(a1)*Math.sin(b1);
  Double d = 6366000 * Math.acos(t1 + t2 + t3);
  if (d.isNaN()) {
    System.err.printf("dist %f %f %f %f\n", lat1, lng1, lat2, lng2);
    System.exit(1);
  }
  return d;


# randomly generate base schedule
def generateBaseSchedule(bandsHashMap):
  bandsHashMap.sortByTimeSlot() # least available timeslots to most
  Schedule schedule = new Schedule();
  totalTimeSlots = schedule.size()
  int[] unassignedBandIDs = []
  currentTimeSlot = 1
  
  # phase 1: place as many bands as possible
  for (int[] bandIDs in bandsWithXTimeSlots.getValues()):
    bandIDs.shuffle() #randomly pick bands one at a time. we choose from the bands with the fewest available time slots first and then go up from there.
    for (id in bandIDs):
      Band band = bandsHashMap.get(id)
      int i = 0
      bool assigned = false
      while (i < totalTimeSlots): # round robin through all time slots and bands
        slot = (currentTimeSlot + i) % totalTimeSlots
        if ( band.availableTimeSlots[slot] && noConflicts(schedule.get(slot), band) ): # band can play at this time
          schedule.add(slot, band)
          currentTimeSlot = slot + 1
          band.slot = slot
          assigned = true
          break
        else:
          i++
      if (!assigned):
        print "no available time slots for " + band.name
        unassignedBandIDs.push(band.id) # will deal with these later...

  # phase 2: deal with the bands that were unable to be assigned in phase 1
  for (uBandID in unassignedBandIDs):
    uBand = bandsHashMap.get(uBandID)
    bool success = false
    for (conflictingBandID in uBand.getConflicts()):
      int oldTimeSlot = band.slot
      success = tryToMoveBand(conflictingBandID)
      if (success):
        schedule.add(oldTimeSlot, uBand)
        break
    if (!success):
      print "this is actually impossible. exit with grace."
      quit()
      
  score(schedule)
  return schedule

# calculate average distance of k nearest neighbors

# score schedule, this is just the max variance over all timeslots
"""
for bands in each time slot
1) get k nearest neighbors
2) calculate average distance from those neighbors and store
3) get variance of that entire timeslot
4) keep track of the largest variance
"""
    
# k is an adjustable amount of nearest neighbors to calculate
def computeVariance(int slot, Schedule sched, k):
  float knnData[] = []
  for (band in sched.getBandsAtSlot(slot)):
    int[] knearest = band.calculateKNearest(k, sched)
    float avg = 0
    for (nearestBand in knearest):
      avg += band.distances.get(nearestBand)
    knnData.push(avg/k) # gets average distance from knn for each band at each time slot
    
  # calculate variance
  sched.timeSlotVariances[slot] = stats_standard_deviation(knnData)**2;
  return sched.timeSlotVariances[slot]

# k is an adjustable amount of nearest neighbors to calculate
def score(Schedule sched, int k):
  for (slot in 1..timeSlots):
    variance = computeVariance(slot, sched, k)
    if (variance > sched.score):
      sched.score = variance 
    
# pairwise swaps to improve the schedule - will need to recompute the variance of the two time slots that are affected by the swap
def improve(Schedule sched):
  int highestVarianceTimeSlot = sched.timeSlotVariances.max # index of highest variance corresponds to highestVarianceTimeSlot
  Band[] bands = sched.getBandsAtSlot(highestVarianceTimeSlot)
  int minDistance = 9999 + 1
  Band bx, by
  for (int i = 0; i < bands.size(); i++):
    Band bi = bands[i]
    for (int j = 0; j < i; j++):
      Band bj = bands[j]
      float d = bi.getDistance(bj)
      if (d < minDistance):
        bx = bi
        by = bj
        minDistance = d
        
  Band br = rnd.nextInt(2) == 0 ? bx : by;
  Schedule newSched = sched.deepCopy()
  newSched.randomSwap(br) # update score in randomSwap()
  if (newSched.score < sched.score):
    sched = newSched
    return true
  return false
  
# create schedule then repeat
def run():
  Schedule result;
  for (int i = 0; i < NUM_SCHEDS_TO_GENERATE; i++):
    Schedule tmp = generateBaseSchedule(bandsHashMap)
    int noImprovements = 0
    while (True):
      noImprovements = improve(tmp) ? 0 : noImprovements + 1
      if (noImprovements == 100):
        break;
    if (result == null || tmp.score < result.score):
    # choose best schedule to export to CSV
      result = tmp
  
  #******* POST PROCESS *******
  result.exportToCSV()



