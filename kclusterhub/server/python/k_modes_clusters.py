import sys
sys.path.insert(0,"/var/www/html/webkmeans/server/api/.venv/lib/python3.11/site-packages")
import os 
os.environ['MPLCONFIGDIR'] = os.getcwd() + "/configs/"
from sklearn.cluster import KMeans
import pandas as pd
from kmodes.kmodes import KModes 
import warnings
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

df1=df.copy()

columns=columns=sys.argv[2].split(',')
clusters=int(sys.argv[3])



kmodes=KModes(n_clusters = clusters, init = 'Cao')
predicted=kmodes.fit_predict(df[columns])
df1['cluster']=predicted+1

columns.append('cluster');

df1[columns].to_csv(sys.argv[5],index=False,encoding='utf-8')


