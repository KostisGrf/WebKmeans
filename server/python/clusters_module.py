from sklearn.cluster import KMeans
import matplotlib.pyplot as plt
import pandas as pd
from sklearn.preprocessing import MinMaxScaler
import sys
import warnings
warnings.filterwarnings('ignore')


if (sys.argv[4]=="csv"):
    df=pd.read_csv(sys.argv[1])
else:
    df=pd.read_excel(sys.argv[1])

df1=df.copy()

columns=columns=sys.argv[2].split(',')
clusters=int(sys.argv[3])

scaler=MinMaxScaler()

for i in range(len(columns)):
    scaler.fit(df[[columns[i]]])
    df[columns[i]]=scaler.transform(df[[columns[i]]])


kmeans=KMeans(n_clusters=clusters,n_init='auto')
predicted=kmeans.fit_predict(df[columns])
df1['cluster']=predicted+1


df1.to_csv(sys.argv[5],index=False,encoding='utf-8')