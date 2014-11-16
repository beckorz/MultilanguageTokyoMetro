#!/usr/bin/python
# -*- coding: utf-8 -*-
import sys
import urllib
import urllib2
import lxml.html
import cookielib
import re


def getTimeInfo(el, className):
  v = el.xpath('./span[@class="' + className + '"]/text()')
  if len(v) > 0:
    return v[0].strip()
  else:
    return ''

def analyzeTokyoMetroStationTimeTable(url, rule):
  req = urllib2.Request(url)
  cj = cookielib.CookieJar()
  opener = urllib2.build_opener()
  opener.add_handler(urllib2.HTTPCookieProcessor(cj))
  conn = opener.open(req)
  cont = conn.read()

  dom = lxml.html.fromstring(cont.decode('utf-8'))
  rows = dom.xpath('//table[@class="dataTable"]//tr')
  for r in rows:
    hour = r.xpath('./th[@class="hour"]')[0].text_content();
    if not hour.isdigit():
      continue

    timeDatas = r.xpath('./td/div/p');
    for tInfo in timeDatas:
      info1 = getTimeInfo(tInfo, 'info01')
      info2 = getTimeInfo(tInfo, 'info02')
      time = '{0}:{1}'.format(hour.zfill(2), info2.zfill(2))
      print time
      #print (r.text_content().encode('utf-8'))

analyzeTokyoMetroStationTimeTable('http://www.tokyometro.jp/station/kokkai-gijidomae/timetable/chiyoda/b/index.html', None)
