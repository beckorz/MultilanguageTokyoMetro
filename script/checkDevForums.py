#!/usr/bin/python
# -*- coding: utf-8 -*-
import sys
import urllib
import urllib2
import lxml.html
import cookielib
import re

if len(sys.argv) != 4:
  print ('python checkDevForums.py email passworf serch-word')
  exit()

url = 'https://developer.tokyometroapp.jp/users/sign_in'
values = {'user[email]' : sys.argv[1],
          'user[password]' : sys.argv[2]}
search = sys.argv[3]
data = urllib.urlencode(values)
req = urllib2.Request(url, data)
cj = cookielib.CookieJar()
opener = urllib2.build_opener()
opener.add_handler(urllib2.HTTPCookieProcessor(cj))
conn = opener.open(req)
cont = conn.read()
dom = lxml.html.fromstring(cont.decode('utf-8'))
contents = dom.xpath('//div[@class="alert alert-success"]')

if len(contents) == 0:
  print ('Permission error.')
  exit()

baseUrl = 'https://developer.tokyometroapp.jp'

def searchPage(url, term):
  r = re.compile(term)
  req = urllib2.Request(url)
  conn = opener.open(req)
  cont = conn.read()
  dom = lxml.html.fromstring(cont.decode('utf-8'))
  contents = dom.xpath('//div[@class="contents col-md-12"]')
  for c in contents:
    text = c.text_content().encode('utf-8')
    if r.search(text):
      print url
      return

def readListPage(url):
  req = urllib2.Request(url)
  conn = opener.open(req)
  cont = conn.read()

  dom = lxml.html.fromstring(cont)
  links = dom.xpath('//div[@class="subject"]/a')

  for l in links:
    if 'href' in l.attrib:
      searchPage(baseUrl + l.attrib['href'], search);
  
  next = dom.xpath('//a[@class="next_page"]')
  for n in next:
    if 'href' in n.attrib:
      return (n.attrib['href']);
  return None

nextPage = '/forum/forums/1'
while nextPage:
  nextPage = readListPage(baseUrl+nextPage)

