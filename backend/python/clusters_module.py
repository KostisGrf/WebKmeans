from sklearn.cluster import KMeans
import matplotlib.pyplot as plt
import pandas as pd
from sklearn.preprocessing import MinMaxScaler



df=pd.read_csv("fuel_prices_52.csv")

columns=['unleaded95','nomos']

scaler=MinMaxScaler()

for i in range(len(columns)):
    scaler.fit(df[[columns[i]]])
    df[columns[i]]=scaler.transform(df[[columns[i]]])

k=3

kmeans=KMeans(n_clusters=k)
predicted=kmeans.fit_predict(df[columns])
df['cluster']=predicted

print(df)
