const { ApolloServer, gql } = require('apollo-server')
const { RESTDataSource } = require('apollo-datasource-rest')

const typeDefs = gql`
  type Phrase {
    words: String
    count: Int
  }
  type Query {
    phrases(doc: String): [Phrase]
    phrasesd(doc: String): [Phrase]
  }
`

const resolvers = {
  Query: {
    phrases: async (_source, { doc }, { dataSources }) => {
      return JSON.parse(await dataSources.phraseAPI.postDoc(doc))
    }
  }
}

class PhraseAPI extends RESTDataSource {
  constructor () {
    super()
  }
  async postDoc (doc) {
    return this.get(`http://localhost:8282/`, doc)
  }
}

const server = new ApolloServer({
  typeDefs,
  resolvers,
  dataSources: () => ({
    phraseAPI: new PhraseAPI()
  })
})

server.listen().then(({ url }) => {
  console.log(`🚀  Server ready at ${url}`)
})
