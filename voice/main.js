'use strict';

const express = require('express')
const app = express()
const port = 3000

app.use(express.json({limit: '50mb'}))

// Imports the Google Cloud client library
const speech = require('@google-cloud/speech');
const fs = require('fs');

app.get('/', (req, res) => {
  res.send('Speech to text server is runing...')
})

app.post('/speech-to-text', async (req, res) => {
  // Creates a client
  const client = new speech.SpeechClient();

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
  const request = {
    audio: audio,
    config: config,
  };

  // Detects speech in the audio file
  const [response] = await client.recognize(request);
  const transcription = response.results
    .map(result => result.alternatives[0].transcript);
 
  // Send response
  res.send(transcription[0])
})

app.listen(port, () => console.log(`App listening on port ${port}!`))
