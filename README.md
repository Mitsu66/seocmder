# SEOCmder

## Description

Seocmder is a php command line tool which can help you in many seo tasks.

**Features :**
- Internal pagerank calculator
- Stemming tool
- Spinner tool

After installation enter help command to know how to use : 
``` 
seocmder --help
```
## Detailed features

### 1. Calculate internal pagerank on your website.

Step 1 : Format the screaming frog outlinks export in a compatible format to seocmder.
```
seocmder pri sf-outlinks screamingfrog_export.csv links.txt
```

Step 2 : Calculate pri and export data on a new file

```
seocmder pri links.txt pri.txt
```

You can define more options, enter that command to know how : 

```
seocmder pri --help
```

### 2. Spinner.

You need to have a masterspin in file and : 

```
seocmder spin masterspin.txt spins.txt -limit=50
```

**limit** is the maximum percent of similarity accepted for your spins.

You can see the help with this command : 

```
seocmder spin --help
```

### 2. Stemmer.

You can extract all stem of an url with that command :

```
seocmder stemmer http://www.mitseo.net export.txt
```


You can define more options, enter that command to know how : 

```
seocmder stemmer --help
```


### Installation
#### 1. Linux

You need to have php installed and php-curl library.
Replace by your own path and add an alias with the console :
```
alias seocmder="php /var/seocmder/seocmder.php"
```

it's ok you can enter **seocmder --help** to confirm installation.
#### 2. Windows

You need to have php installed and php-curl library. You can install wamp server on windows : [Download Wamp Server](http://www.wampserver.com/)

Replace by your own path :

```
doskey seocmder=C:\wamp64\bin\php\php7.0.10\php.exe C:\test\test.php $*
```

When the installation is completed, you can then enter **seocmder --help** .