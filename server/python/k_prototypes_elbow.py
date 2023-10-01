# Import module for data manipulation
import pandas as pd
# Import module for k-protoype cluster
from kmodes.kmodes import KModes 
from kmodes.kprototypes import KPrototypes
from sklearn.cluster import KMeans
import numpy as np
from sklearn.preprocessing import StandardScaler
from sklearn.preprocessing import StandardScaler, OneHotEncoder
# Ignore warnings
import warnings
import matplotlib.pyplot as plt
from sklearn.preprocessing import MinMaxScaler
warnings.filterwarnings('ignore', category = FutureWarning)
# Format scientific notation from Pandas
pd.set_option('display.float_format', lambda x: '%.3f' % x)

from kneed import KneeLocator

# Load the data
df = pd.read_csv('server/python/datasets/public_datasets/magic/magic.csv')

# df1=df[["LastName","Gender","Country","Age"]]
df1=df[["FLength","FWidth","FSize","FConc","FConc1","FAsym","FM3Long","FM3Trans","FAlpha","FDist","Class"]]

catColumnsPos = [df1.columns.get_loc(col) for col in list(df1.select_dtypes('object').columns)]

num_cols=df1._get_numeric_data().columns
scaler=MinMaxScaler()
cost = []
clusters=15
dfMatrix = df1.to_numpy()

numerical_columns = df1.select_dtypes(include=[np.number]).columns
categorical_columns = df1.select_dtypes(include=['object']).columns

print(categorical_columns)

# Define batch size
batch_size = 20

# Initialize lists to store within-cluster sum of squares (WCSS) for different K values
wcss_values = []

# Determine WCSS for different K values using the elbow method
for k in range(1, 11):  # Testing K values from 1 to 10
    wcss = 0
    
    # Perform batch K-Prototypes clustering and calculate WCSS
    for batch_idx in range(len(df1) // batch_size + 1):
        start_idx = batch_idx * batch_size
        end_idx = (batch_idx + 1) * batch_size
        batch_data = df1.iloc[start_idx:end_idx]

        numerical_features = batch_data[numerical_columns]
        categorical_features = batch_data[categorical_columns]

        print(categorical_features)

        # One-hot encode categorical features
        one_hot_encoder = OneHotEncoder()
        encoded_categorical_features = one_hot_encoder.fit_transform(categorical_columns)
#         encoded_categorical_features = encoded_categorical_features.toarray()

#         # Combine scaled numerical and encoded categorical features for K-Prototypes
#         mixed_data = np.hstack((numerical_features.to_numpy(), encoded_categorical_features))

#         kprototypes = KPrototypes(n_clusters=k, init='Cao')
#         kprototypes.fit(mixed_data, categorical=list(range(numerical_features.shape[1], mixed_data.shape[1])))

#         # Calculate the distance of each point to its cluster center and sum it
#         distances = np.sum(kprototypes.cost_)
#         wcss += distances
    
#     wcss_values.append(wcss)

# # Plot the elbow curve
# plt.plot(range(1, 11), wcss_values, marker='o')
# plt.xlabel('Number of Clusters (K)')
# plt.ylabel('WCSS')
# plt.title('Elbow Method for K-Prototypes')
# plt.show()
# # Plot the elbow curve
# plt.plot(range(1, 11), wcss_values, marker='o')
# plt.xlabel('Number of Clusters (K)')
# plt.ylabel('WCSS')
# plt.title('Elbow Method for K-Prototypes')
# plt.show()

# if(len(catColumnsPos)>0):
#     if(len(num_cols)>0):
#         for i in range(len(num_cols)):
#             scaler.fit(df[[num_cols[i]]])
#             df[num_cols[i]]=scaler.transform(df[[num_cols[i]]])
#         for cluster in range(1,clusters+1):
#             try:
#                 kprototype = KPrototypes(n_jobs = -1, n_clusters = cluster, init = 'Huang', max_iter=50, n_init=2)
#                 kprototype.fit_predict(dfMatrix, categorical = catColumnsPos)
#                 cost.append(kprototype.cost_)
#                 print('Cluster initiation: {}'.format(cluster))
#             except:
#                 print("breaked at " , cluster)
#                 clusters=cluster-1
#                 break
#     else:
#             for cluster in range(1,clusters+1):
#                 kmodes = KModes(n_jobs = -1, n_clusters = cluster, init = 'Huang',max_iter=50)
#                 predicted=kmodes.fit(dfMatrix)
#                 cost.append(predicted.cost_)
#                 print('Cluster initiation: {}'.format(cluster))
# else:
#      for i in range(len(num_cols)):
#             scaler.fit(df[[num_cols[i]]])
#             df[num_cols[i]]=scaler.transform(df[[num_cols[i]]])
#      for i in range(1,clusters+1):
#         kmeans = KMeans(n_clusters=i,n_init='auto')
#         kmeans.fit(dfMatrix)
#         cost.append(kmeans.inertia_)
     

# result=list(map(str, cost))
# kl=KneeLocator(range(1,clusters+1),cost,curve="convex",direction="decreasing")    

# print(kl.elbow)

# kprototype = KPrototypes(n_jobs = 1, n_clusters = 9, init = 'Huang')
# predicted=kprototype.fit_predict(dfMatrix, categorical = catColumnsPos)
# df['cluster']=predicted+1

# plt.figure(figsize=(16,8))
# plt.plot(range(1,clusters+1), cost, 'bx-')
# plt.xlabel('k')
# plt.ylabel('Distortion')
# plt.title('The Elbow Method showing the optimal k')
# plt.show()








    

