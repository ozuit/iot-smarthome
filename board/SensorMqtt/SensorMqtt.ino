#include "DHTesp.h"
#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include "MD5.h"
#include <stdio.h> 
#include <time.h>
#include <stdlib.h>
#include <WiFiUdp.h>
#include <NTPClient.h>

#define DHTPIN 4

const char* ssid     = "MINH THU 5";
const char* password = "55555555";
const char* mqtt_server = "94.237.73.225";
const char* secret_key = "";
const long utcOffsetInSeconds = 0;

// Initializes the espClient
WiFiClient espClient;
PubSubClient client(espClient);
DHTesp dht;

// Define NTP Client to get time
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org", utcOffsetInSeconds);

void setup_wifi() {
  delay(10);
  // We start by connecting to a WiFi network
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("WiFi connected - ESP IP address: ");
  Serial.println(WiFi.localIP());
}

char* signature(char* payload)
{
  unsigned long ts = timeClient.getEpochTime();

  String str_payload = String(payload);
  str_payload.trim();
  String hash_data = String(ts) + '|' + str_payload + '|' + String(secret_key);

  Serial.println(hash_data);

  char * cstr = new char [hash_data.length()+1];
  strcpy (cstr, hash_data.c_str());

  unsigned char* hash = MD5::make_hash(cstr);
  char *md5str = MD5::make_digest(hash, 16);

  String signed_payload = String(md5str) + '|' + String(ts) + '|' + str_payload;
  char * cstr_signed = new char [signed_payload.length()+1];
  strcpy (cstr_signed, signed_payload.c_str());
  
  return cstr_signed;
}
 
void setup()
{
  dht.setup(DHTPIN, DHTesp::DHT22);
  Serial.begin(115200);
  setup_wifi();
  timeClient.begin();
  client.setServer(mqtt_server, 1883);
}
 
void loop()
{
  timeClient.update();
  
  TempAndHumidity measurement = dht.getTempAndHumidity();
 
  Serial.print("Temperature: ");
  Serial.println(measurement.temperature);
 
  Serial.print("Humidity: ");
  Serial.println(measurement.humidity);

  Serial.println(signature("aaaaa"));

  static char temperatureTemp[7];
  dtostrf(measurement.temperature, 6, 2, temperatureTemp);

  static char humidityTemp[7];
  dtostrf(measurement.humidity, 6, 2, humidityTemp);
    
  if(!client.loop()) {
    client.connect("ESP8266Client");
  }

  client.publish("smarthome/living-room/sensor/temp/sensor1", signature(temperatureTemp));
  client.publish("smarthome/living-room/sensor/hum/sensor1", signature(humidityTemp));

  delay(2000);
}
