import { ApolloClient, InMemoryCache } from '@apollo/client';

export const client = new ApolloClient({
  // uri: 'http://13.61.69.45/graphql',
  uri: 'http://localhost:8000/graphql',
  cache: new InMemoryCache(),
});
