# -*- coding: utf-8 -*-
# easy_install python_twitter
import os
import re
import sys
import twitter
from collections import defaultdict
from math import log
import json

import codecs
reload(sys)
sys.setdefaultencoding('utf-8')
sys.stdout = codecs.getwriter('utf-8') (sys.stdout)
import MeCab

class TwitterAnalyzer:
  def __init__(self,usrdicdir,dicdir,consumer_key,consumer_secret,access_token_key,access_token_secret):
    p = ""
    #if dicdir == "":
    #  p = "-u%s" % (usrdicdir)
    #else:
    #  p = " -d%s -u%s" % (dicdir ,usrdicdir)
    self.api = twitter.Api(base_url="https://api.twitter.com/1.1",
                  consumer_key=consumer_key,
                  consumer_secret=consumer_secret, 
                  access_token_key=access_token_key, 
                  access_token_secret=access_token_secret) 
    self.mecab = MeCab.Tagger(p)
    self.wordcount = defaultdict(int)

  def AnalyzeTerm(self, statuses, remove_term):
    self.wordcount = defaultdict(int)
    for s in statuses:
      txt = s.text.encode('utf-8')
      txt = txt.replace(remove_term, '')
      self.morph( txt )
    ret = []
    max=0
    for k, v in sorted(self.wordcount.items(), key=lambda x:x[1], reverse=True):
      if v > max:
        max = v

    for k, v in sorted(self.wordcount.items(), key=lambda x:x[1], reverse=True):
      if v == 1 and max > 1:
        break
      word = k
      word = word.replace("\"","")
      word = word.replace("\'","")
      word = word.replace("\\","\\\\")

      ret.append( {"text":word ,"weight":v} )
    return ret

  def SearchPlace(self, lat, long, radius):
    statuses = self.api.GetSearch( geocode=[lat, long, radius], count=100, result_type='mixed')
    return self.AnalyzeTerm(statuses, '')

  def SearchTerm(self, search_term):
    # statuses = self.api.GetSearch( geocode=['35.65858','139.745433','0.5km'], count=100, result_type='mixed')
    statuses = self.api.GetSearch(term=search_term,  count=100, result_type='mixed')
    return self.AnalyzeTerm(statuses, search_term)

  def morph(self,text):
    pos = [u'名詞',u'形容詞', u'形容動詞',u'感動詞',u'動詞',u'副詞'] #u'形容詞', u'形容動詞',u'感動詞',u'副詞',u'連体詞',u'名詞',u'動詞']
    #pos = [u'名詞']
    exclude=[
      u'RT',
      u'TL',
      u'sm',
      u'#',
      u'さん',
      u'ある',
      u'する',
      u'いる',
      u'やる',
      u'これ',
      u'それ',
      u'あれ',
      '://',
      u'こと',
      u'の',
      u'そこ',
      u'ん',
      u'なる',
      u'http',
      u'https',
      u'co',
      u'jp',
      u'com'
    ]
    node = self.mecab.parseToNode(text)
    while node:
      fs = node.feature.split(",")
      if fs[0] in pos:
        word = (fs[6] != '*' and fs[6] or node.surface)
        word = word.strip()
        if word.isdigit() == False:
          if len(word)!=1:
            if word not in exclude:
              self.wordcount[word] += 1
      node = node.next
