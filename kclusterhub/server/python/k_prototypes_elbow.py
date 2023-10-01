import sys
sys.path.insert(0,"/var/www/html/webkmeans/server/api/.venv/lib/python3.11/site-packages")
import os 
os.environ['MPLCONFIGDIR'] = os.getcwd() + "/configs/"
# Import module for data manipulation
import pandas as pd
pd.options.mode.chained_assignment = None
# Import module for k-protoype cluster
from kmodes.kmodes import KModes 
from kmodes.kprototypes import KPrototypes
from sklearn.cluster import KMeans
# Ignore warnings
import warnings
import sys
import matplotlib.pyplot as plt
from sklearn.preprocessing import MinMaxScaler
import json
warnings.filterwarnings('ignore', category = FutureWarning)
# Format scientific notation from Pandas
pd.set_option('display.float_format', lambda x: '%.3f' % x)
pd.options.mode.chained_assignment = None

from kneed import KneeLocator




if(sys.argv[5]==','):
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



if(len(df1)>=1800):
     sample_size=1000 
else:
     sample_size=len(df1)     
sampled_data = df1.sample(n=sample_size)

cost = []

for i in range(len(num_cols)):
            scaler.fit(sampled_data[[num_cols[i]]])
            sampled_data[num_cols[i]]=scaler.transform(sampled_data[[num_cols[i]]])

dfMatrix = sampled_data.to_numpy()



for cluster in range(1,clusters+1):
    try:
        kprototype = KPrototypes(n_clusters = cluster, init = 'Cao' ,max_iter=50,n_init=2)
        kprototype.fit(dfMatrix, categorical = catColumnsPos)
        cost.append(kprototype.cost_)
    except:
        clusters=cluster-1
        break       
     

result=list(map(str, cost))
kl=KneeLocator(range(1,clusters+1),cost,curve="convex",direction="decreasing")    


print(json.dumps({"sse":result,"suggested-k":str(kl.elbow)}))












    

