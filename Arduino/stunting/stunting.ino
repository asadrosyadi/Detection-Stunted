#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <RBDdimmer.h>
#include <Wire.h>
#include <Arduino.h>
#include <HX711_ADC.h>
#if defined(ESP8266)|| defined(ESP32) || defined(AVR)
#include <EEPROM.h>
#endif
#include <ESP32SharpIR.h>
#include <math.h>
#include <LiquidCrystal_I2C.h>

LiquidCrystal_I2C lcd(0x27, 16, 2);

// Lingkar Kepala
// Definisi sensor dan pin yang terhubung
ESP32SharpIR sensor1(ESP32SharpIR::GP2Y0A21YK0F, 34);
ESP32SharpIR sensor2(ESP32SharpIR::GP2Y0A21YK0F, 33);
ESP32SharpIR sensor3(ESP32SharpIR::GP2Y0A21YK0F, 32);
ESP32SharpIR sensor4(ESP32SharpIR::GP2Y0A21YK0F, 35);
// Diameter lingkar kepala (dalam cm)
const float headDiameter = 45.0;
// Fungsi untuk menghitung lingkar kepala
float calculateHeadCircumference(float d1, float d2, float d3, float d4) {
    // Menghitung jarak efektif dari setiap sensor ke titik-titik pada lingkar kepala
    float r1 = sqrt(pow(headDiameter / 2, 2) + pow(d1, 2));
    float r2 = sqrt(pow(headDiameter / 2, 2) + pow(d2, 2));
    float r3 = sqrt(pow(headDiameter / 2, 2) + pow(d3, 2));
    float r4 = sqrt(pow(headDiameter / 2, 2) + pow(d4, 2));
    // Menghitung estimasi lingkar kepala dengan interpolasi atau regresi
    // Misalnya, bisa menggunakan rata-rata atau interpolasi linear dari keempat jarak efektif
    float estimatedCircumference = (r1 + r2 + r3 + r4) / 4.0;
    return estimatedCircumference;
}

// ketinggian ultrasonic
const int trigPin = 27;
const int echoPin = 26;
//define sound speed in cm/uS
#define SOUND_SPEED 0.034
#define CM_TO_INCH 0.393701
long duration;
float distanceCm;

//pins Load Cells:
const int HX711_dout = 15; //mcu > HX711 dout pin
const int HX711_sck = 5; //mcu > HX711 sck pin
//HX711 constructor:
HX711_ADC LoadCell(HX711_dout, HX711_sck);
const int calVal_eepromAdress = 0;
unsigned long t = 0;


const char* ssid = "wifi rumah";
const char* password = "12345678";
String linkGET = "http://192.168.1.17:8081/stunting/rest/bacajason/";
String kirim_server = "http://192.168.1.17:8081/stunting/rest/kirimdatasensor";

void setup() {
  Serial.begin(115200);
  lcd.begin();
  // Hubungkan ke WiFi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Gagal terhubung ke WiFi. Mengulangi...");
  }
  Serial.println("Terhubung ke WiFi!");

  // Sharp IR
  // Setting filter resolution to 0.1
    sensor1.setFilterRate(0.1f);
    sensor2.setFilterRate(0.1f);
    sensor3.setFilterRate(0.1f);
    sensor4.setFilterRate(0.1f);

  // ultrasonic
  pinMode(trigPin, OUTPUT); // Sets the trigPin as an Output
  pinMode(echoPin, INPUT); // Sets the echoPin as an Input

// Load Cell
float calibrationValue; // calibration value
  
#if defined(ESP8266) || defined(ESP32)
  EEPROM.begin(512); // uncomment this if you use ESP8266 and want to fetch this value from eeprom
#endif
  EEPROM.get(calVal_eepromAdress, calibrationValue); // uncomment this if you want to fetch this value from eeprom

  LoadCell.begin();
  //LoadCell.setReverseOutput();
  unsigned long stabilizingtime = 2000; // tare preciscion can be improved by adding a few seconds of stabilizing time
  boolean _tare = true; //set this to false if you don't want tare to be performed in the next step
  LoadCell.start(stabilizingtime, _tare);
  if (LoadCell.getTareTimeoutFlag()) {
    Serial.println("Timeout, check MCU>HX711 wiring and pin designations");
  }
  else {
    LoadCell.setCalFactor(calibrationValue); // set calibration factor (float)
    Serial.println("Startup is complete");
  }
  while (!LoadCell.update());
  Serial.print("Calibration value: ");
  Serial.println(LoadCell.getCalFactor());
  Serial.print("HX711 measured conversion time ms: ");
  Serial.println(LoadCell.getConversionTime());
  Serial.print("HX711 measured sampling rate HZ: ");
  Serial.println(LoadCell.getSPS());
  Serial.print("HX711 measured settlingtime ms: ");
  Serial.println(LoadCell.getSettlingTime());
  Serial.println("Note that the settling time may increase significantly if you use delay() in your sketch!");
  if (LoadCell.getSPS() < 7 || LoadCell.getSPS() > 100 ) {
    Serial.println("HX711 not found.");
    while(1){}
  }

  lcd.setCursor(0,0);
  lcd.print("System Ready");

}

void loop() {
    // Load Cell
  static boolean newDataReady = 0;
  const int serialPrintInterval = 500; //increase value to slow down serial print activity
  // check for new data/start next conversion:
  if (LoadCell.update()) newDataReady = true;  
    if (newDataReady) {
    if (millis() > t + serialPrintInterval) {
      float mass = LoadCell.getData();
      if(mass<0)mass=0;
      Serial.print("Load_cell output val: ");
      Serial.println(mass);
      newDataReady = 0;
      t = millis();

  // receive command from serial terminal, send 't' to initiate tare operation:
  if (Serial.available() > 0) {
    char inByte = Serial.read();
    if (inByte == 't') LoadCell.tareNoDelay();
  }
  // check if last tare operation is complete:
  if (LoadCell.getTareStatus() == true) {
    Serial.println("Tare complete");
  }
  delay (100);

  // Mengukur ketinggian Ultrasonic
  // Clears the trigPin
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  // Sets the trigPin on HIGH state for 10 micro seconds
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);
  // Reads the echoPin, returns the sound wave travel time in microseconds
  duration = pulseIn(echoPin, HIGH);
  // Calculate the distance
  distanceCm = duration * SOUND_SPEED/2;
  float height = 130.0 - distanceCm;
  Serial.print("Height: ");
  Serial.println(height); 

  // Mengukur Lingkar Kepala
  // Membaca jarak dari setiap sensor
    float distance1 = sensor1.getDistanceFloat();
    float distance2 = sensor2.getDistanceFloat();
    float distance3 = sensor3.getDistanceFloat();
    float distance4 = sensor4.getDistanceFloat();

    // Menghitung lingkar kepala dengan menggunakan jarak dari setiap sensor
    float head = calculateHeadCircumference(distance1, distance2, distance3, distance4);

    // Menampilkan hasil di Serial Monitor
    Serial.print("Head Circumference: ");
    Serial.println(head);

  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("M");
  lcd.setCursor(5,0);
  lcd.print("H");
  lcd.setCursor(10,0);
  lcd.print("He");

  int mass2 = mass;
  int height2 = height;
  int head2 = head;
  lcd.setCursor(0,1);
  lcd.print(mass2);
  lcd.setCursor(5,1);
  lcd.print(height2);
  lcd.setCursor(10,1);
  lcd.print(head2);

  // Mengambil Data Jason
  // Buat koneksi HTTP
  HTTPClient http;
  http.begin(linkGET);

  // Lakukan permintaan GET
  int httpCode = http.GET();

  // Jika permintaan berhasil
  if (httpCode == HTTP_CODE_OK) {
    String payload = http.getString();
    //Serial.println(payload);

    // Parse data JSON
    DynamicJsonDocument doc(4096);
    deserializeJson(doc, payload);
    JsonObject data = doc["Data"][0];

    // Ambil data yang diperlukan
    String status = data["status"];
    Serial.println(status);

    if (status == "ON"){
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("Get Command");
    lcd.setCursor(0,1);
    lcd.print("Processing........");
    delay(300);
    
    // Kirim
      if(WiFi.status()== WL_CONNECTED){
      HTTPClient http;
      String serverPath = kirim_server + "?&&mass=" + mass + "&&height=" + height + "&&head=" + head;
      // Your Domain name with URL path or IP address with path
      http.begin(serverPath.c_str());
      // Send HTTP GET request
      int httpResponseCode = http.GET();
      if (httpResponseCode>0) {
        //Serial.print("HTTP Response code: ");
        //Serial.println(httpResponseCode);
        String payload = http.getString();
        //Serial.println(payload);
      }
      else {
        //Serial.print("Error code: ");
        //Serial.println(httpResponseCode);
      }
      // Free resources
      http.end();
    }
    }
    else {
    //Serial.println("OFF");
    } 
  
  } else {
    Serial.println("Gagal melakukan permintaan HTTP");
  }
  http.end();  // Putuskan koneksi HTTP
}
  }
}
