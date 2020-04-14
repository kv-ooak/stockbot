#!/usr/bin/env python
# -*- coding: utf-8 -*- 
import mechanize
import urllib
import time
import zipfile
import shutil
import mysql.connector
#apt-get install python-mysql.connector

Root = '/var/www/StockBot/'
#Root = './'

config = {
      'user': 'root',
      'password': '',
      'host': '127.0.0.1',
      'database': 'StockBot',
      'raise_on_warnings': True,
    }

def downloadFile(date):
    br = mechanize.Browser()
    br.set_handle_robots(False)
    
    #Login
    br.open("http://www.cophieu68.vn/account/login")
    #print [form for form in br.forms()][2]
    br.select_form(name = "frm")
    br['username'] = "hoangducquang1993@gmail.com"
    br['tpassword'] = 'midoban'
    result = br.submit(name='login', label='ĐĂNG NHẬP').read()
    
    #Download
    br.retrieve('http://www.cophieu68.vn/export/metastock_all.php', Root + 'script/download/metastock_all_' + date + '.zip')[0]
    #br.retrieve('http://www.cophieu68.vn/export/companylist.php', 'companylist_' + date + '.csv')[0]
    

def extractFile(date):
    #Extract
    with zipfile.ZipFile(Root + 'script/download/metastock_all_' + date + '.zip', "r") as z:
        z.extractall(Root + 'script/download/metastock_all_' + date)
    #Copy and rename extracted file
    shutil.move(Root + 'script/download/metastock_all_' + date + '/datax123456/metastock_all_data.txt', Root + 'storage/app/metastock_all_' + date + '.txt')
    #Delete extracted folder
    shutil.rmtree(Root + 'script/download/metastock_all_' + date)
    
def importFileToSql(file):
    cnx = mysql.connector.connect(**config)
    cursor = cnx.cursor()
    
    add_file = ("INSERT INTO data_files "
               "(filename, mime, original_filename, data_type, created_at, updated_at) "
               "VALUES (%s, %s, %s, %s, %s, %s)")
    now = time.strftime("%Y-%m-%d %H:%M:%S")
    data = (file, 'text/plain', file, 2, now, now)
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
    data_import = ('default', '{"job":"Illuminate\\\Foundation\\\Console\\\QueuedJob","data":["command:ImportData",{"filename":"' + file + '","--truncate":"1"}]}', 0, 0 , now, now)
    data_bot = ('default', '{"job":"Illuminate\\\Foundation\\\Console\\\QueuedJob","data":["command:CalculateBot"]}', 0, 0 , now, now)
    data_recommend = ('default', '{"job":"Illuminate\\\Foundation\\\Console\\\QueuedJob","data":["command:CalculateRecommend",{"--date":"' + date + '"}]}', 0, 0 , now, now)
    cursor.execute(add_job, data_import)
    cursor.execute(add_job, data_bot)
    cursor.execute(add_job, data_recommend)
    cnx.commit()
    cursor.close()
    cnx.close()
    
def main():
    date = time.strftime("%Y%m%d")
    downloadFile(date)
    extractFile(date)
    
    file = 'metastock_all_' + date + '.txt'
    importFileToSql(file)
    addJobs(file)

main()
