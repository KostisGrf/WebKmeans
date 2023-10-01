import sys
sys.path.insert(0,"/var/www/html/webkmeans/server/api/.venv/lib/python3.11/site-packages")
import os 
os.environ['MPLCONFIGDIR'] = os.getcwd() + "/configs/"
# Import module for data manipulation
import pandas as pd
# Import module for k-protoype cluster
from kmodes.kmodes import KModes 
# Ignore warnings
import warnings
import sys
import json
import matplotlib.pyplot as plt
from sklearn.preprocessing import MinMaxScaler
warnings.filterwarnings('ignore', category = FutureWarning)
# Format scientific notation from Pandas
pd.set_option('display.float_format', lambda x: '%.3f' % x)
pd.options.mode.chained_assignment = None
import numpy as np

from kneed import KneeLocator



if(sys.argv[5]==','):
    delimiter=','
else:
    delimiter=';'

if (sys.argv[4]=="csv"):
    df=pd.read_csv(sys.argv[1],sep=delimiter)
else:
    df=pd.read_excel(sys.argv[1])

columns=sys.argv[2].split(',')
clusters=int(sys.argv[3])

df1=df[columns]


cost = []


if(len(df1)>=1800):
     sample_size=1000 
else:
     sample_size=len(df1)     
sampled_data = df1.sample(n=sample_size)

dfMatrix = sampled_data.to_numpy()


for cluster in range(1,clusters+1):
    kmodes = KModes(n_clusters = cluster, init = 'Cao',n_init=2)
    predicted=kmodes.fit(dfMatrix)
    cost.append(predicted.cost_)
     

result=list(map(str, cost))
kl=KneeLocator(range(1,clusters+1),cost,curve="convex",direction="decreasing")



print(json.dumps({"sse":result,"suggested-k":str(kl.elbow)}))










    

