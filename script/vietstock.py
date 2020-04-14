#!/usr/bin/env python
import urllib
import urllib2
import json
import time
import datetime
import socket
import shutil
import mysql.connector
#apt-get install python-mysql.connector

serverVersion = [0, 0, 0]
IndexCode1 = 'VNINDEX'
CatID1 = 1
OutputFile1 = 'HSX'
IndexCode2 = 'HASTCINDEX'
CatID2 = 2
OutputFile2 = 'HNX'
OutputFile = 'price'
QuoteFile = 'quote'
Domain = 'http://banggia.vietstock.vn/'
Interval = 3 #seconds
RequestTotalCount = 0
RequestErrorCount = 0

Root = '/var/www/StockBot/'
#Root = './'

config = {
      'user': 'root',
      'password': '',
      'host': '127.0.0.1',
      'database': 'StockBot',
      'raise_on_warnings': True,
    }

# Get Price Update Data
def getupdateprice(date, version, IndexCode, CatID, OutputFile):
    global Interval
    global RequestTotalCount
    global RequestErrorCount
    global serverVersion
    
    url = Domain + 'StockHandler.ashx?option=rt&getVersion=' + str(serverVersion[version]) + '&IndexCode=' + str(IndexCode) + '&catid=' + str(CatID)
    values = {}
    data = urllib.urlencode(values)
    req = urllib2.Request(url, data)
    try:
        response = urllib2.urlopen(req, timeout = Interval)
        result = json.loads(response.read())
        #print result
        tmp = json.loads(result['listvalue'])
        #tmpcss = json.loads(result['listcss']) #unused
        
        if result['serverVersion'] > serverVersion[version]:
            serverVersion[version] = result['serverVersion']
            #prefix = str(serverVersion[version])
            prefix = (datetime.datetime.utcnow() + datetime.timedelta(hours=7)).strftime("%H:%M:%S") #GMT+7 timezone
            f = open(Root + 'script/output/' + OutputFile + '_' + date + '.txt', 'a')
            for item in tmp:
                #print item[0]
                f.write(prefix + item[0] + '\n')
            f.close()
        RequestTotalCount = RequestTotalCount + 1
        
    except urllib2.URLError as e:
        RequestErrorCount = RequestErrorCount + 1
        print type(e)
        
    except socket.timeout as e:
        RequestErrorCount = RequestErrorCount + 1
        print type(e)

def isDataValid(a, b, c):
    return a != 'ATO' and b != 'ATO' and c != 'ATO' and a != 'ATC' and b != 'ATC' and c != 'ATC' and a != '' and b != '' and c != ''

def moveFile(file):
    #Copy and rename extracted file
    shutil.move(Root + 'script/output/' + file, Root + 'storage/app/' + file)
    
def importFileToSql(file):
    cnx = mysql.connector.connect(**config)
    cursor = cnx.cursor()
    
    add_file = ("INSERT INTO data_files "
               "(filename, mime, original_filename, data_type, created_at, updated_at) "
               "VALUES (%s, %s, %s, %s, %s, %s)")
    now = time.strftime("%Y-%m-%d %H:%M:%S")
    data = (file, 'text/plain', file, 3, now, now)
    cursor.execute(add_file, data)
    cnx.commit()
    cursor.close()
    cnx.close()
    
def addJobs(file):
    cnx = mysql.connector.connect(**config)
    cursor = cnx.cursor()
    
    add_job = ("INSERT INTO jobs "
               "(queue, payload, attempts, reserved, available_at, created_at) "
               "VALUES (%s, %s, %s, %s, %s, %s)")
    now = time.time()
    date = time.strftime("%Y-%m-%d")
    data_import = ('default', '{"job":"Illuminate\\\Foundation\\\Console\\\QueuedJob","data":["command:ImportQuote",{"filename":"' + file + '","--truncate":"0"}]}', 0, 0 , now, now)
    cursor.execute(add_job, data_import)
    cnx.commit()
    cursor.close()
    cnx.close()
    
# Main function
def main():
    global Interval
    global RequestTotalCount
    global RequestErrorCount
    global serverVersion
    
    date = time.strftime("%Y%m%d")
    #Overwrite setting
    #date = '20160314'
    getDataFromServer = True
    calculateData = True
    importFile = True
    
    start1 = '090000' #9:00:00
    end1 = '113010' #11:30:10
    start2 = '130000' #13:00:00
    end2 = '150010' #15:00:10
    
    #Get data from vietstock site
    while getDataFromServer:
        hour = (datetime.datetime.utcnow() + datetime.timedelta(hours=7)).strftime("%H%M%S") #GMT+7 timezone
        print 'Loop: ' + str(RequestTotalCount) + ' - Error: ' + str(RequestErrorCount) + ' - ' + hour

        if (start1 <= hour <= end1) or (start2 <= hour <= end2):
            getupdateprice(date, 1, IndexCode1, CatID1, OutputFile)
            getupdateprice(date, 2, IndexCode2, CatID2, OutputFile)
        else:
            if hour > end2:
                print 'End Bot'
                break
            print 'Break'
            
        time.sleep(Interval)
        
    #Process
    if calculateData:
        filename = Root + 'script/output/' + OutputFile + '_' + date + '.txt'
        
        data = []
        with open(filename, "r") as ins:
            for line in ins:
                tmp = line.rstrip('\n').split('|')
                data.append([tmp[4], tmp[0], tmp[12], tmp[13], tmp[17], tmp[25]]) #Ticker 0, Hour 1, Bid 2, Price 3, Ask 4, Total Volume 5
        
        data.sort(key=lambda array: array[0])
        
        data_range = len(data)
        data_temp = []
        #Filter 1
        for x in range(0, data_range):
            if x + 1 < data_range and data[x][0] == data[x+1][0] and data[x][5] == data[x+1][5]: #filter same volume data
                continue
            
            #case (vol == total && price > ask):
            if (int(data[x][5]) - int(data[x-1][5]) == int(data[x][5])) and (isDataValid(data[x][2], data[x][3], data[x][4]) and float(data[x][3]) > float(data[x][4])):
                check = 'BUY'
            #case (price == ask):
            elif (data[x][3] == data[x][4]):
                check = 'BUY'
            #case (ticker == prev_ticker && price == prev_ask):
            elif (data[x][0] == data[x-1][0]) and (data[x][3] == data[x-1][4]):
                check = 'BUY'
            #case (price - bid > ask - price):
            elif isDataValid(data[x][2], data[x][3], data[x][4]) and (float(data[x][3]) - float(data[x][2]) > float(data[x][4]) - float(data[x][3])):
                check = 'BUY'
            #case (vol == total && price < bid):
            elif (int(data[x][5]) - int(data[x-1][5]) == int(data[x][5])) and (isDataValid(data[x][2], data[x][3], data[x][4]) and float(data[x][3]) < float(data[x][2])):
                check = 'SELL'
            #case (price == bid):
            elif (data[x][3] == data[x][2]):
                check = 'SELL'
            #case (ticker == prev_ticker && price == prev_bid):
            elif (data[x][0] == data[x-1][0]) and (data[x][3] == data[x-1][2]):
                check = 'SELL'
            #case (price - bid < ask - price):
            elif isDataValid(data[x][2], data[x][3], data[x][4]) and (float(data[x][3]) - float(data[x][2]) < float(data[x][4]) - float(data[x][3])):
                check = 'SELL'
            else:
                check = 'NA'
                
            data_temp.append([data[x][0], data[x][1], data[x][2], data[x][3], data[x][4], data[x][5], check]) #Ticker 0, Hour 1, Bid 2, Price 3, Ask 4, Total Volume 5, Order 6
        
        f = open(Root + 'script/output/' + QuoteFile + '_' + date + '.txt', 'w')
        data_temp_range = len(data_temp)
        #Filter 2
        for x in range(0, data_temp_range):
            if data_temp[x][0] == data_temp[x - 1][0]:
                volume = str(int(data_temp[x][5]) - int(data_temp[x - 1][5]))
            else:
                volume = data_temp[x][5]
                
            #Ticker, Date, Hour, Bid, Price, Ask, Volume, Total Volume, Order
            print data_temp[x][0] + ',' + date + ',' + data_temp[x][1] + ',' + data_temp[x][2] + ',' + data_temp[x][3] + ',' + data_temp[x][4] + ',' + volume + ',' + data_temp[x][5] + ',' + data_temp[x][6]
            f.write(data_temp[x][0] + ',' + date + ',' + data_temp[x][1] + ',' + data_temp[x][2] + ',' + data_temp[x][3] + ',' + data_temp[x][4] + ',' + volume + ',' + data_temp[x][5] + ',' + data_temp[x][6] + '\n')
        f.close()
        
    if importFile:
        moveFile(QuoteFile + '_' + date + '.txt')
        importFileToSql(QuoteFile + '_' + date + '.txt')
        addJobs(QuoteFile + '_' + date + '.txt')

    
# Call Main Function
main()
