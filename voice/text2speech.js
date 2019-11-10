const textToSpeech = require('@google-cloud/text-to-speech');
const fs = require('fs');
const util = require('util');

(main = async () => {
    const client = new textToSpeech.TextToSpeechClient();

    /**
     * TODO(developer): Uncomment the following lines before running the sample.
     */
    const text = 'Xin chào, Tôi có thể giúp gì cho bạn nào?';
    const outputFile = 'assistant_hello.mp3';
    
    const request = {
      input: {text: text},
      voice: {languageCode: 'vi-VN', ssmlGender: 'FEMALE'},
      audioConfig: {audioEncoding: 'MP3'},
    };
    const [response] = await client.synthesizeSpeech(request);
    const writeFile = util.promisify(fs.writeFile);
    await writeFile(outputFile, response.audioContent, 'binary');
    console.log(`Audio content written to file: ${outputFile}`);
})()
