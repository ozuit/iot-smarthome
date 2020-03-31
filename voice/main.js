'use strict';

const axios = require('axios')
const express = require('express')
const app = express()
const port = process.env.PORT || 3000

app.use(express.json({ limit: '50mb' }))

app.use(function(request, response, next) {
  response.header("Access-Control-Allow-Origin", "*");
  response.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
  next();
});

// Load config
require('dotenv').config()

const axiosInstance = axios.create({
  baseURL: process.env.API_ENPOINT,
});

// Imports the Google Cloud client library
const speech = require('@google-cloud/speech');
const textToSpeech = require('@google-cloud/text-to-speech');
const fs = require('fs');
const queryIntent = require('./agent');

// Keeping the context across queries let's us simulate an ongoing conversation with the bot
let contexts;

app.get('/', (req, res) => {
  res.send('IoT agent is runing...')
})

app.post('/iotagent', async (req, res) => {
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
  const timeS2T_begin = Math.floor(Date.now());
  const [responseS2T] = await clientS2T.recognize(requestS2T);
  const timeS2T_end = Math.floor(Date.now());
  console.log('S2T: ' + (timeS2T_end - timeS2T_begin))

  const transcription = responseS2T.results
    .map(result => result.alternatives[0].transcript);

  // Query to Dialogflow
  if (transcription[0]) {
    const timeNLP_begin = Math.floor(Date.now());
    const responseDialogflow = await queryIntent(transcription[0], contexts);
    const timeNLP_end = Math.floor(Date.now());
    console.log('NLP: ' + (timeNLP_end - timeNLP_begin))

    contexts = responseDialogflow.outputContexts;
    let responseText = responseDialogflow.fulfillmentText;
    
    if (responseText.indexOf('nhiệt độ') != -1) {
      const response = await axiosInstance.get(`/api/${process.env.INTERNAL_TOKEN}/iot-agent/info?type=temp`)
      if (response.data.status) {
        responseText += ' ' + response.data.value + ' độ C';
      }
    } else if (responseText.indexOf('độ ẩm') != -1) {
      const response = await axiosInstance.get(`/api/${process.env.INTERNAL_TOKEN}/iot-agent/info?type=hum`)
      if (response.data.status) {
        responseText += ' ' + response.data.value + '%';
      }
    } else if (responseText.indexOf('chế độ') != -1) {
      if (responseText.indexOf('đi làm') != -1) {
        axiosInstance.put(`/api/${process.env.INTERNAL_TOKEN}/iot-agent/turn-off-all`)
      } else if (responseText.indexOf('đi ngủ') != -1) {
        axiosInstance.put(`/api/${process.env.INTERNAL_TOKEN}/iot-agent/sleep-mode`)
      } else if (responseText.indexOf('xem phim') != -1) {
        axiosInstance.put(`/api/${process.env.INTERNAL_TOKEN}/iot-agent/movie-mode`)
      } else if (responseText.indexOf('đọc sách') != -1) {
        axiosInstance.put(`/api/${process.env.INTERNAL_TOKEN}/iot-agent/book-mode`)
      } else {
        responseText = 'Chế độ chưa được cài đặt'
      }
    } else {
      axiosInstance.put(`/api/${process.env.INTERNAL_TOKEN}/iot-agent/update`, {
        fulfillment: responseText
      })
    }

    const requestT2S = {
      input: { text: responseText },
      // Select the language and SSML Voice Gender (optional)
      voice: { languageCode: 'vi-VN', ssmlGender: 'NEUTRAL' },
      // Select the type of audio encoding
      audioConfig: { audioEncoding: 'MP3' },
    };

    // Performs the Text-to-Speech request
    const timeT2S_begin = Math.floor(Date.now());
    const [responseT2S] = await clientT2S.synthesizeSpeech(requestT2S);
    const timeT2S_end = Math.floor(Date.now());
    console.log('T2S: ' + (timeT2S_end - timeT2S_begin))

    // Send response
    res.send(responseT2S.audioContent.toString('base64'))
  } else {
    res.send('')
  }
})

app.listen(port, () => console.log(`App listening on port ${port}!`))
