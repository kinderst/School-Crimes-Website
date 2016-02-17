#Scott Kinder, Final

import mysql.connector
from mysql.connector import errorcode
import csv

def to_null(item):
	if item == '':
		return 0
	else:
		return int(item)

def is_ascii(s):
    return all(ord(c) < 128 for c in s)

conn = mysql.connector.connect(user='root', password='sd1ee4an94', port=5794, host='kinders.vergil.u.washington.edu', database='schoolcrime')

cur = conn.cursor(buffered=True)

#csvfile = open('residencehallarrest101112.csv')

#cur.execute("CREATE TABLE Market (FMID serial PRIMARY KEY, marketName varchar, website varchar, facebook varchar, twitter varchar, youtube varchar, otherMedia varchar, street varchar, city varchar, county varchar, state varchar, zip varchar, locationType varchar);")
#cur.execute("CREATE TABLE Product (productFMID serial PRIMARY KEY, organic char(1), bakedGoods char(1), cheese char(1), crafts char(1), flowers char(1), eggs char(1), seafood char(1), herbs char(1), vegetables char(1), honey char(1), jams char(1), maple char(1), meat char(1), nursery char(1), nuts char(1), plants char(1), poultry char(1), prepared char(1), soap char(1), trees char(1), wine char(1), coffee char(1), beans char(1), fruits char(1), grains char(1), juices char(1), mushrooms char(1), petFood char(1), tofu char(1), wildHarvest char(1));")
#cur.execute("CREATE TABLE Payment (paymentFMID serial PRIMARY KEY, credit char(1), wic char(1), wicCash char(1), sfmnp char(1), snap char(1));")

#datareader = csv.DictReader(csvfile, delimiter=',')

#locationTypeName = "residencehall"
#cur.execute("SELECT location_type_id FROM location_type WHERE type_name = %s", (locationTypeName,))
#locationTypeId = cur.fetchone()
#crimeTypeName = "arrest"
locationTypesArray = ['publicproperty', 'oncampus', 'noncampus']
crimeTypeArray = ['arrest', 'crime', 'discipline']
for locationTypeName in locationTypesArray:
	for crimeTypeName in crimeTypesArray:
		csvfile = open(locationTypeName + crimeTypeName + '101112.csv')
		datareader = csv.DictReader(csvfile, delimiter=',')
		cur.execute("SELECT location_type_id FROM location_type WHERE type_name = %s", (locationTypeName,))
		locationTypeId = cur.fetchone()
		for line in datareader:
			#is in an american state
			if line['State'] != None and line['State'] != '':
					#if people go to this school
					if line["men_total"] != '' or line["women_total"] != '':
						if is_ascii(line['Address']) and is_ascii(line['INSTNM']) and "?" not in line['INSTNM']:
							#if no non-utf8 chars
							cur.execute("SELECT EXISTS(SELECT * FROM schools WHERE school_name = %s)", (line['INSTNM'],))
							row = cur.fetchone()
							#the school doesnt exist in db yet
							if row[0] == False:
								cur.execute("SELECT state_id FROM states WHERE state_abbv = %s", (line['State'],))
								stateId = cur.fetchone()
								cur.execute("SELECT EXISTS(SELECT * FROM sectors WHERE sector_name = %s)", (line['Sector_desc'].lower(),))
								row = cur.fetchone()
								#sector doesnt exist yet
								if row[0] == False:
									cur.execute("INSERT INTO sectors (sector_name) VALUES (%s)", (line['Sector_desc'].lower(),))
								cur.execute("SELECT sector_id FROM sectors WHERE sector_name = %s", (line['Sector_desc'].lower(),))
								sectorId = cur.fetchone()
								cur.execute("""INSERT INTO schools (school_name, state_id, num_of_men, num_of_women, address, city, zip, sector_id) VALUES ('%s', %s, %s, %s, '%s', '%s', '%s', %s)""" % (conn.converter.escape(line["INSTNM"]), stateId[0], line["men_total"], line["women_total"], conn.converter.escape(line['Address']), conn.converter.escape(line["City"]), line["ZIP"], sectorId[0]))
								#a = 'b'
								#cur.execute("""INSERT INTO schools (school_name, state_id, num_of_men, num_of_women, address, city, zip, sector_id) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)""" % (a, 1, 2, 3, a, a, a, 1))

								print "school inserted: " + line['INSTNM']

							cur.execute("SELECT school_id FROM schools WHERE school_name = %s", (line['INSTNM'],))
							schoolId = cur.fetchone()
							cur.execute("""SELECT EXISTS(SELECT * FROM crime_records WHERE school_id = %s AND location_type_id = %s)""" % (schoolId[0], locationTypeId[0]))
							row = cur.fetchone()
							#make sure record for the school exist
							if row[0] == False:
								cur.execute("""INSERT INTO crime_records (school_id, location_type_id) VALUES (%s, %s)""" % (schoolId[0], locationTypeId[0]))
								print ("Inserted new crime record for: " + line['INSTNM'])
							cur.execute("""SELECT record_id FROM crime_records WHERE school_id = %s AND location_type_id = %s""" % (schoolId[0], locationTypeId[0]))
							recordId = cur.fetchone()

							if crimeTypeName == 'arrest':
								#ARREST!
								
								#check if record for college in arrest
								#see if record exists, if so add to it
								for year in range(10, 13):
									cur.execute("SELECT EXISTS (SELECT * FROM arrest WHERE record_id = %s AND year = %s)", (recordId[0], year))


									row = cur.fetchone()
									if row[0] == False:
										cur.execute("""INSERT INTO arrest (record_id, year, weapon, drug, liquor) VALUES (%s, %s, %s, %s, %s)""" % (recordId[0], year, to_null(line['WEAPON' + str(year)]), to_null(line['DRUG' + str(year)]), to_null(line['LIQUOR' + str(year)])))
										print ("Just inserted arrest record for: " + line['INSTNM'] + " and year: " + str(year))					
									else:
										cur.execute("""SELECT weapon, drug, liquor FROM arrest WHERE record_id = %s AND year = %s""" % (recordId[0], year))
										arrestInfo = cur.fetchone()
										cur.execute("""UPDATE arrest SET weapon = %s, drug = %s, liquor = %s WHERE record_id = %s AND year = %s""" % ((arrestInfo[0] + to_null(line['WEAPON' + str(year)])), (arrestInfo[1] + to_null(line['DRUG' + str(year)])), (arrestInfo[2] + to_null(line['LIQUOR' + str(year)])), recordId[0], year))
										print ("UPDATED arrest record, and updates crime records for: " + line['INSTNM'] + " and year: " + str(year))
							
							elif crimeTypeName == 'discipline':
								for year in range(10, 13):
									cur.execute("""SELECT EXISTS (SELECT * FROM discipline WHERE record_id = %s AND year = %s)""" % (recordId[0], year))

									row = cur.fetchone()
									if row[0] == False:
										cur.execute("""INSERT INTO discipline (record_id, year, weapon, drug, liquor) VALUES (%s, %s, %s, %s, %s)""" % (recordId[0], year, to_null(line['WEAPON' + str(year)]), to_null(line['DRUG' + str(year)]), to_null(line['LIQUOR' + str(year)])))
										print ("Just inserted discipline record for: " + line['INSTNM'] + " and year: " + str(year))
									else:
										cur.execute("""SELECT weapon, drug, liquor FROM discipline WHERE record_id = %s AND year = %s""" % (recordId[0], year))
										disciplineInfo = cur.fetchone()
										cur.execute("""UPDATE arrest SET weapon = %s, drug = %s, liquor = %s WHERE record_id = %s AND year = %s""" % ((disciplineInfo[0] + to_null(line['WEAPON' + str(year)])), (disciplineInfo[1] + to_null(line['DRUG' + str(year)])), (disciplineInfo[2] + to_null(line['LIQUOR' + str(year)])), recordId, year))
										print ("Just updated discipline record, due to duplicate entry in csv file for: " + line['INSTNM'] + " and year: " + str(year))
							else:
							#Crime
								for year in range(10, 13):
									cur.execute("""SELECT EXISTS (SELECT * FROM crime WHERE record_id = %s AND year = %s)""" % (recordId[0], year))
									row = cur.fetchone()
									if row[0] == False:
										cur.execute("""INSERT INTO crime (record_id, year, murd, negm, forcib, nonfor, robbe, agga, burgla, vehic, arson) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)""" % (recordId[0], year, to_null(line['MURD' + str(year)]), to_null(line['NEG_M' + str(year)]), to_null(line['FORCIB' + str(year)]), to_null(line['NONFOR' + str(year)]), to_null(line['ROBBE' + str(year)]), to_null(line['AGG_A' + str(year)]), to_null(line['BURGLA' + str(year)]), to_null(line['VEHIC' + str(year)]), to_null(line['ARSON' + str(year)])))
										print ("Just inserted crime record for: " + line['INSTNM'] + " and year: " + str(year))
									else:
										cur.execute("""SELECT murd, negm, forcib, nonfor, robbe, agga, burgla, vehic, arson FROM crime WHERE record_id = %s AND year = %s""" % (recordId[0], year))
										crimeInfo = cur.fetchone()
										cur.execute("""UPDATE crime SET murd = %s, negm = %s, forcib = %s, nonfor = %s, robbe = %s, agga = %s, burgla = %s, vehic = %s, arson = %s WHERE record_id = %s AND year = %s""" % ((crimeInfo[0] + to_null(line['MURD' + str(year)])), (crimeInfo[1] + to_null(line['NEG_M' + str(year)])), (crimeInfo[2] + to_null(line['FORCIB' + str(year)])), (crimeInfo[3] + to_null(line['NONFOR' + str(year)])), (crimeInfo[4] + to_null(line['ROBBE' + str(year)])), (crimeInfo[5] + to_null(line['AGG_A' + str(year)])), (crimeInfo[6] + to_null(line['BURGLA' + str(year)])), (crimeInfo[7] + to_null(line['VEHIC' + str(year)])), (crimeInfo[8] + to_null(line['ARSON' + str(year)])), recordId[0], year))
										print ("Just UPDATED crime record for: " + line['INSTNM'] + " and year: " + str(year))
							'''
							#Hate
							#if there is already a hate record
							#cur.execute("""SELECT EXISTS(SELECT * FROM hate WHERE record_id = %s AND year = %s)""" % (recordId[0], year))
							#row = cur.fetchone()
							#if row[0] == False:
							#	cur.execute("INSERT INTO hate (record_id, year) VALUES (%s, %s)", (recordId, year))
							#else:

							#cur.execute("SELECT hate_id FROM hate WHERE record_id = %s AND year = %s)", (recordId, year))
							#hateId = cur.fetchone()
							'''

conn.commit()

cur.close()
conn.close()

print "Completeeeeeee!"