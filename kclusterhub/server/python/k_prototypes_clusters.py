import sys
sys.path.insert(0,"/var/www/html/webkmeans/server/api/.venv/lib/python3.11/site-packages")
import os 
os.environ['MPLCONFIGDIR'] = os.getcwd() + "/configs/"
from sklearn.cluster import KMeans
import pandas as pd
pd.options.mode.chained_assignment = None
from kmodes.kmodes import KModes 
from kmodes.kprototypes import KPrototypes
from sklearn.preprocessing import MinMaxScaler
import warnings
import json
warnings.filterwarnings('ignore')
pd.options.mode.chained_assignment = None




if(sys.argv[6]==','):
    delimiter=','
else:
    delimiter=';'

if (sys.argv[4]=="csv"):
    df=pd.read_csv(sys.argv[1],sep=delimiter)
else:
    df=pd.read_excel(sys.argv[1])

columns=columns=sys.argv[2].split(',')
clusters=int(sys.argv[3])

df1=df[columns]

catColumnsPos = [df1.columns.get_loc(col) for col in list(df1.select_dtypes('object').columns)]

num_cols=df1._get_numeric_data().columns
scaler=MinMaxScaler()
for i in range(len(num_cols)):
            scaler.fit(df1[[num_cols[i]]])
            df1[num_cols[i]]=scaler.transform(df1[[num_cols[i]]])
dfMatrix = df1.to_numpy()

try:
    kprototype = KPrototypes(n_clusters = clusters, init = 'Cao' ,max_iter=50,n_init=2)
    predicted=kprototype.fit_predict(dfMatrix, categorical = catColumnsPos)
    df['cluster']=predicted+1
except:
    error=True
    print(error)
    exit(1)
  
columns.append('cluster');

df[columns].to_csv(sys.argv[5],index=False,encoding='utf-8')

error=False
print(error)
