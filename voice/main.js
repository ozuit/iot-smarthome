'use strict';

const express = require('express')
const app = express()
const port = 3000

app.use(express.json({ limit: '50mb' }))

// app.use(function(request, response, next) {
//   response.header("Access-Control-Allow-Origin", "*");
//   response.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
//   next();
// });

// Imports the Google Cloud client library
const speech = require('@google-cloud/speech');
const textToSpeech = require('@google-cloud/text-to-speech');
const fs = require('fs');
const queryIntent = require('./agent');

// Keeping the context across queries let's us simulate an ongoing conversation with the bot
let contexts;

app.get('/', (req, res) => {
  res.send('Speech to text server is runing...')
})

app.post('/speech-to-text', async (req, res) => {
  // Creates a client
  const clientS2T = new speech.SpeechClient();
  const clientT2S = new textToSpeech.TextToSpeechClient();

  // The name of the audio file to transcribe
  const fileName = './test.wav';

  // Reads a local audio file and converts it to base64
  const file = fs.readFileSync(fileName);
  const audioBytes = req.body.audio || file.toString('base64');

  // The audio file's encoding, sample rate in hertz, and BCP-47 language code
  const audio = {
    content: audioBytes,
  };
  const config = {
    encoding: 'LINEAR16',
    languageCode: 'vi-VN',
  };
  const requestS2T = {
    audio: audio,
    config: config,
  };

  // Detects speech in the audio file
  const [responseS2T] = await clientS2T.recognize(requestS2T);
  const transcription = responseS2T.results
    .map(result => result.alternatives[0].transcript);

  // Query to Dialogflow
  const responseDialogflow = await queryIntent(transcription[0], contexts);
  contexts = responseDialogflow.outputContexts;

  const requestT2S = {
    input: { text: responseDialogflow.fulfillmentText },
    // Select the language and SSML Voice Gender (optional)
    voice: { languageCode: 'vi-VN', ssmlGender: 'NEUTRAL' },
    // Select the type of audio encoding
    audioConfig: { audioEncoding: 'MP3' },
  };

  // Performs the Text-to-Speech request
  const [responseT2S] = await clientT2S.synthesizeSpeech(requestT2S);

  // Send response
  res.send(responseT2S.audioContent.toString('base64'))
})

app.listen(port, () => console.log(`App listening on port ${port}!`))
