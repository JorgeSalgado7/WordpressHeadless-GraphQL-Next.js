import { ApolloProvider } from '@apollo/client'
import client from '../libs/apollo-client'

function MyApp({ Component, pageProps }) {
	return (
		<ApolloProvider client={client}>
			<Component {...pageProps} />
		</ApolloProvider>
	)
}

export default MyApp
