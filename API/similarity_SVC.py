# coding:utf-8
# onlyzs 2016/08/18
# SVC

from gensim import corpora, models, similarities
import scipy as sp
import csv
from sklearn.ensemble import RandomForestClassifier
from sklearn import preprocessing
import numpy as np
from sklearn.utils import column_or_1d
import argparse
from sklearn.svm import SVC
from sklearn import cross_validation
from sklearn.multiclass import OneVsRestClassifier

def estimate_data(data, label, test):
	classifier = SVC(C=1., kernel='rbf', gamma=0.01)
	estimator  = OneVsRestClassifier(classifier)
	estimator.fit( data, column_or_1d(label) )
	print estimator.decision_function( test )
	test_vec = len(test)
	for vec in range(test_vec):
		result = []
		for index, esti in enumerate(estimator.classes_):
			result.append([esti, estimator.decision_function( test )[vec][index]])
		result = sorted(result, key=lambda item: -item[1])	
			
			
		print "Recommendation of user %d: " % ( vec + 1 )
		for res in result:
			if( res[1] != 0):
				print "%s: %f" % ( res[0], res[1] )

def load_file(args):
	le = preprocessing.LabelEncoder()
	data = []
	label = []
	test = []
	with open( args.data[0],'rb' ) as f:
		try:
			reader = csv.reader(f)
			header = reader.next()
			for row in reader:
				data.append( row[:(len(row)-1)] )
				label.append( row[(len(row)-1):] )
		except csv.Error, e:
			sys.exit('file %s, line %d: %s' % (filename, reader.line_num, e))
	
	data_vec = len(data)
	
	with open( args.user[0],'rb' ) as f:
		try:
			reader = csv.reader(f)
			header = reader.next()
			for row in reader:
				data.append( row )
		except csv.Error, e:
			sys.exit('file %s, line %d: %s' % (filename, reader.line_num, e))

	# text to number
	#data = np.array(data).transpose()
	data = zip(*data)
	fited = []
	for da in data:
		if not da[0].replace(".","",1).isdigit():
			fited.append(le.fit_transform( list(da) ))
		else:
			fited.append( da )

	#data = np.array(fited).transpose()
	fited = zip(*fited)
	data = []
	for da in fited:
		data.append(list(da))
	
	test = data[(data_vec):]
	data = data[:(data_vec):]

	print "data: %s" % data
	print "label: %s" % label
	print "test: %s" % test
	
	return data, label, test
	
def main( args ):
	data, label, test = load_file(args)
	estimate_data(data, label, test)


if __name__ == "__main__":
	parser = argparse.ArgumentParser()
	parser.add_argument('-data', nargs=1, required=True)	#train data
	parser.add_argument('-user', nargs=1, required=True)	#user data
	args = parser.parse_args()

	main( args )
