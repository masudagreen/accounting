#Options -Indexes
Order allow,deny
Allow from all
#last 20120327
#google
SetEnvIf User-Agent "Googlebot" ng_ua
deny from 66.249.64.0/19
deny from 74.125.0.0/16
deny from 72.14.192.0/18
#yahoo
SetEnvIf User-Agent "Yahoo! Slurp" ng_ua
deny from 72.30.0.0/16
deny from 67.195.0.0/16
deny from 124.83.191.0/24
deny from 124.83.128.0/17
deny from 211.14.8.0/24
deny from 66.228.160.0/19
deny from 211.14.11.144/28
deny from 124.83.128.0/17
deny from 114.111.64.0/18
deny from 211.14.9.240/28
deny from 74.6.0.0/16
deny from 67.195.0.0/16
deny from 202.160.176.0/20
deny from 110.75.160.0/19
#bing
SetEnvIf User-Agent "msnbot" ng_ua
deny from 65.52.0.0/14
deny from 207.46.0.0/16
deny from 157.54.0.0/15
deny from 202.96.51.128/25
deny from 70.37.0.0/17
#trend micro
deny from 216.104.15.
deny from 216.104.0.0/19
deny from 150.70.84.
deny from 150.70.0.0/16
deny from 66.180.82.
deny from 66.180.80.0/20
deny from 150.70.64.
deny from 150.70.75.
deny from 150.70.66.
deny from 150.70.0.0/16
deny from 150.70.
#baidu
SetEnvIf User-Agent "Baidu" deny_ua
deny from 119.63.192.
deny from 119.63.193.
deny from 119.63.194.
deny from 119.63.195.
deny from 119.63.196.
deny from 119.63.197.
deny from 119.63.198.
deny from 119.63.199.
deny from 216.104.32.0/20
deny from 69.10.32.0/19
deny from 70.40.192.0/19
deny from 72.167.0.0/16
deny from 96.31.64.0/19
deny from 213.186.32.0/19
deny from 74.50.96.0/19
deny from 174.132.0.0/15
deny from 61.14.160.0/19
deny from 122.152.128.0/18
deny from 125.252.64.0/18
deny from 202.147.0.0/18
deny from 203.192.160.0/19
deny from 210.232.15.16/29
#FQDN
deny from 61.199.218.120/29
#goo
deny from 203.131.248.0/21
deny from 210.173.180.0/24
deny from 218.213.128.0/20
deny from 218.213.0.0/16
deny from 222.151.192.80/29
#naver
SetEnvIf User-Agent "Yeti" deny_ua
deny from 119.235.224.0/20
deny from 61.247.192.0/19
deny from 119.235.237.0/24
#potaru
deny from 113.33.116.170
#twitter
deny from 128.242.0.0/16
deny from 122.219.128.185
#alexa
deny from 204.236.128.0/17
deny from 174.129.0.0/16
SetEnvIf User-Agent "WebKo" deny_ua
SetEnvIf User-Agent "ia_archiver" deny_ua
#ask.com
deny from 66.235.112.0/20
#CommonCrawl
deny from 38.0.0.0/8
#Covario IDS
deny from 50.16.0.0/14
#Discovery Engine
deny from 38.0.0.0/8
#dotbot
deny from 208.115.111.240/28
#facebookexternalhit
deny from 66.220.144.0/20
#FairShare
deny from 64.41.128.0/17
deny from 209.249.0.0/16
#Flight Deck Reports
deny from 173.203.0.0/16
deny from 208.113.64.0/18
#Gigablast
deny from 67.16.0.0/15
#HuaweiSymantecSpider
deny from 69.28.48.0/20
#In-A-Gist
deny from 72.14.176.0/20
#Jaxified
deny from 68.233.224.0/19
#LinkedIn
deny from 64.74.0.0/16
#metadata labs
deny from 66.219.32.0/19
#Moreover
deny from 64.94.0.0/15
#OneRiot
deny from 216.24.128.0/19
#PuritySearch
deny from 91.205.96.0/22
deny from 174.132.0.0/15
deny from 195.42.102.0/23
#Robot Genius
deny from 208.96.0.0/18
#ScoutJet
deny from 38.0.0.0/8
#SiteSell.com
deny from 38.0.0.0/8
#The Ellerdale Project
deny from 64.13.128.0/18
#TinEye
deny from 184.72.0.0/15
#Turnitin
deny from 38.0.0.0/8
#VideoSurf
deny from 174.120.0.0/14
#al_viewer
deny from 74.222.0.0/19
#Twitturly
deny from 67.202.0.0/18
deny from 204.236.128.0/17
#Voyager
deny from 38.0.0.0/8
#Topsy
deny from 74.112.128.0/22
#Worio
deny from 174.129.0.0/16
#movatwi
deny from 184.72.0.0/15
deny from 204.236.128.0/17
#Arachmo
SetEnvIf User-Agent "Arachmo" deny_ua
#blogram
deny from 211.130.164.128/25
#PagesInventory
SetEnvIf User-Agent "PagesInventory" deny_ua
#Tasap
deny from 218.28.0.0/15
#sogou
deny from 220.160.0.0/11
#soso
deny from 124.114.0.0/15
#youdao
deny from 61.135.0.0/16
#Daum
deny from 211.232.0.0/15
#GAIS
deny from 140.120.0.0/13
#Python-urllib
deny from 184.72.0.0/15
#PycURL
deny from 174.129.0.0/16
#MetaURI API
deny from 75.101.128.0/17
#JS-Kit URL Resolver
deny from 204.236.128.0/17
#bitlybot
deny from 204.236.128.0/17
#WebCorpusBuilder
deny from 184.72.0.0/15
#es_com_viewer
deny from 74.222.0.0/19
#url_test
deny from 74.222.0.0/19
#Swarm
deny from 76.192.0.0/10
#Ezooms
deny from 208.115.96.0/19
#SheenBot
deny from 184.72.0.0/15
deny from 204.236.128.0/17
#research-scan-bot
deny from 184.72.0.0/15
deny from env=ng_ua
#GeoHasher
deny from 174.120.0.0/14
#mxbot
deny from 67.207.192.0/20

