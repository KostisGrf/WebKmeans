from sklearn.cluster import KMeans
import matplotlib.pyplot as plt
import pandas as pd
from sklearn.preprocessing import MinMaxScaler
import sys
import os



df=pd.read_csv(sys.argv[1],encoding='utf-8')

columns=columns=sys.argv[2].split(',')
clusters=int(sys.argv[3])

scaler=MinMaxScaler()

for i in range(len(columns)):
    scaler.fit(df[[columns[i]]])
    df[columns[i]]=scaler.transform(df[[columns[i]]])



kmeans=KMeans(n_clusters=clusters)
predicted=kmeans.fit_predict(df[columns])
df['cluster']=predicted



df.to_csv(sys.argv[4],encoding='utf-8')
