import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import App from './App.jsx'

import { ApolloClient, HttpLink, InMemoryCache, gql} from "@apollo/client";
import { ApolloProvider } from "@apollo/client/react";

/* Initialize Apollo Client to manage GraphQL state and requests */
const client = new ApolloClient({
  link: new HttpLink({ uri: import.meta.env.VITE_GRAPHQL_URL }),
  cache: new InMemoryCache(),
})

createRoot(document.getElementById('root')).render(
  <StrictMode>
    {/* Apollo Provider to wrap the entire app to provide GraphQL access to all components */}
    <ApolloProvider client={client}> 
      <App />
    </ApolloProvider>
  </StrictMode>,
)
