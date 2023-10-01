import sys
sys.path.insert(0,"/var/www/html/webkmeans/server/api/.venv/lib/python3.11/site-packages")
import os 
os.environ['MPLCONFIGDIR'] = os.getcwd() + "/configs/"
# Import module for data manipulation
import pandas as pd
# Import module for k-protoype cluster
from kmodes.kmodes import KModes 
from kmodes.kprototypes import KPrototypes
from sklearn.cluster import KMeans
import numpy as np
# Ignore warnings
import warnings
import matplotlib.pyplot as plt
from sklearn.preprocessing import MinMaxScaler
import json
from kneed import KneeLocator
warnings.filterwarnings('ignore', category = FutureWarning)
# Format scientific notation from Pandas
pd.options.mode.chained_assignment = None
pd.set_option('display.float_format', lambda x: '%.3f' % x)



if(sys.argv[6]==','):
    delimiter=','
else:
    delimiter=';'

# Load the data
if (sys.argv[4]=="csv"):
    df=pd.read_csv(sys.argv[1],sep=delimiter)
else:
    df=pd.read_excel(sys.argv[1])

columns=sys.argv[2].split(',')
clusters=int(sys.argv[3])

df1=df[columns]

catColumnsPos = [df1.columns.get_loc(col) for col in list(df1.select_dtypes('object').columns)]

num_cols=df1._get_numeric_data().columns
scaler=MinMaxScaler()


if(len(catColumnsPos)>0):
    if(len(num_cols)>0):
        for i in range(len(num_cols)):
           for i in range(len(num_cols)):
            scaler.fit(df1[[num_cols[i]]])
            df1[num_cols[i]]=scaler.transform(df1[[num_cols[i]]])
            dfMatrix = df1.to_numpy()
        for cluster in range(1,clusters+1):
            try:
                kprototype = KPrototypes(n_clusters = cluster, init = 'Cao', max_iter=50, n_init=3)
                predicted=kprototype.fit_predict(dfMatrix, categorical = catColumnsPos)
                df['cluster']=predicted+1
            except Exception as e:
                error=True
                print(error)
                exit(1)
    else:
            for cluster in range(1,clusters+1):
                dfMatrix = df1.to_numpy()
                kmodes = KModes(n_clusters = cluster, init = 'Cao',max_iter=50)
                predicted=kmodes.fit_predict(dfMatrix)
                df['cluster']=predicted+1
else:
     for i in range(len(num_cols)):
            scaler.fit(df1[[num_cols[i]]])
            df1[num_cols[i]]=scaler.transform(df1[[num_cols[i]]])
     for i in range(1,clusters+1):
        kmeans = KMeans(n_clusters=i,n_init='auto')
        predicted=kmeans.fit_predict(df1)
        df['cluster']=predicted+1




columns.append('cluster');

df[columns].to_csv(sys.argv[5],index=False,encoding='utf-8')

error=False
print(error)

