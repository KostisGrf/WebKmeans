# Import module for data manipulation
import pandas as pd
# Import module for k-protoype cluster
from kmodes.kmodes import KModes 
from kmodes.kprototypes import KPrototypes
from sklearn.cluster import KMeans
# Ignore warnings
import warnings
import sys
import matplotlib.pyplot as plt
from sklearn.preprocessing import MinMaxScaler
warnings.filterwarnings('ignore', category = FutureWarning)
# Format scientific notation from Pandas
pd.set_option('display.float_format', lambda x: '%.3f' % x)

from kneed import KneeLocator

if (sys.argv[4]=="csv"):
    df=pd.read_csv(sys.argv[1])
else:
    df=pd.read_excel(sys.argv[1])

columns=sys.argv[2].split(',')
clusters=int(sys.argv[3])

df1=df[columns]


cost = []
clusters=10
dfMatrix = df1.to_numpy()



for cluster in range(1,clusters+1):
    kmodes = KModes(n_jobs = -1, n_clusters = cluster, init = 'Huang')
    predicted=kmodes.fit(dfMatrix)
    cost.append(predicted.cost_)
     

result=list(map(str, cost))
kl=KneeLocator(range(1,clusters+1),cost,curve="convex",direction="decreasing")    

print(kl.elbow)










    

