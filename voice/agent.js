'use strict';

// [START dialogflow_quickstart]

const dialogflow = require('dialogflow');
const uuid = require('uuid');
const projectId = 'iotagent-gvrspf';

/**
 * Send a query to the dialogflow agent, and return the query result.
 * @param {string} projectId The project to be used
 */
module.exports = async function queryIntent(queryRequest) {
  // A unique identifier for the given session
  const sessionId = uuid.v4();

  // Create a new session
  const sessionClient = new dialogflow.SessionsClient();
  const sessionPath = sessionClient.sessionPath(projectId, sessionId);

  // The text query request.
  const request = {
    session: sessionPath,
    queryInput: {
      text: {
        // The query to send to the dialogflow agent
        text: queryRequest,
        // The language used by the client
        languageCode: 'vi-VN',
      },
    },
  };

  // Send request and log result
  const responses = await sessionClient.detectIntent(request);
  const result = responses[0].queryResult;
  return result;
}
// [END dialogflow_quickstart]