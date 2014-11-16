# -*- coding: utf-8 -*-
# easy_install python_twitter
import sys
import twitter
from twitter_analyzer import TwitterAnalyzer
import json

def main(argvs, argc):
  if argc != 6:
    print "Usage: python analyzeTwitterTag consumer_key consumer_secret access_token_key access_token_secret search_term"
    return -1

  consumer_key = argvs[1]
  consumer_secret = argvs[2]
  access_token_key = argvs[3]
  access_token_secret = argvs[4]
  search_term = argvs[5]
  try:
    tw = TwitterAnalyzer('','',consumer_key,consumer_secret,access_token_key,access_token_secret)
    ret = tw.SearchTerm(search_term)
    print (json.dumps(ret))
    return 0
  except twitter.TwitterError, (strerror):
    print strerror
    return 254


if __name__ == '__main__':
  argvs = sys.argv
  argc = len(argvs)
  sys.exit(main(argvs, argc))

