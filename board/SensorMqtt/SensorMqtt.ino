#include "DHTesp.h"
#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include "MD5.h"
#include <stdio.h> 
#include <time.h>
#include <stdlib.h>
#include <WiFiUdp.h>
#include <NTPClient.h>
#include <Wire.h>
#include "BH1750FVI.h"

BH1750FVI LightSensor;

#define DHTPIN 16 // D0 on esp8266

const char* ssid     = "Dung Trang";
const char* password = "ozu@1234";
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
  
  LightSensor.begin();
  LightSensor.SetAddress(Device_Address_H);
  LightSensor.SetMode(Continuous_H_resolution_Mode);

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

  static char temperatureTemp[7];
  dtostrf(measurement.temperature, 6, 2, temperatureTemp);

  static char humidityTemp[7];
  dtostrf(measurement.humidity, 6, 2, humidityTemp);
    
  if(!client.loop()) {
    client.connect("ESP8266Client");
  }

  client.publish("smarthome/living-room/sensor/temp/sensor1", signature(temperatureTemp));
  client.publish("smarthome/living-room/sensor/hum/sensor1", signature(humidityTemp));

  uint16_t lux = LightSensor.GetLightIntensity();
  Serial.print("Light: ");
  Serial.println(lux);
  float luxFloat = lux;
  static char luxChar[7];
  dtostrf(luxFloat, 6, 2, luxChar);
  client.publish("smarthome/living-room/sensor/light/sensor1", signature(luxChar));

  int gas = analogRead(A0);
  Serial.print("Gas: ");
  Serial.println(gas);
  float gasFloat = gas;
  static char gasChar[7];
  dtostrf(gasFloat, 6, 2, gasChar);
  client.publish("smarthome/living-room/sensor/gas/sensor1", signature(gasChar));

  delay(2000);
}
