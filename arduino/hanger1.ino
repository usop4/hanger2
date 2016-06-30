/*
 * 光るハンガー用スケッチ
 * LilyPadを利用
 * シリアルで「1234」と入力すると
 * 1番目のPixelにr=2,g=3,b=4の値が設定される
 * 「0000」と入力すると全てリセットされる
 * 「7777」と入力するとルーレット
 */

#include <Adafruit_NeoPixel.h>
#include <avr/power.h>

// NeoPixel

int pin = 3; //GEMMA->1,LilyPad->3
int numpixels = 10;

int interval = 1;

long t = 0;
long old_t = 0;

int r;
int g;
int b;

Adafruit_NeoPixel pixels = Adafruit_NeoPixel(numpixels, pin, NEO_GRB + NEO_KHZ800);

void setup() {
  pixels.begin(); // This initializes the NeoPixel library.
  Serial.begin(9600);

  //Serial.println("Hello");
  roulette();

}

void loop() {

  t = millis();

  if( ( t - old_t ) > 1000UL * 3UL ){
    int val = analogRead(A2);
    Serial.println(val);
    old_t = t;
  }

  int num = 0;
  int n = 0;
  byte buff[4];

  while (Serial.available()){
    buff[num] = (int)Serial.read() - 48;
    Serial.print(buff[num]);
    switch(num){
      case 0:
        n = buff[num];
        break;
      case 1:
        r = buff[num];
        break;
      case 2:
        g = buff[num];
        break;
      case 3:
        b = buff[num];
        liteMyHanger(n,r,g,b);
        if( r==7 && g==7 &&b==7 ){
          roulette();
        }
        Serial.println();
        break;
    }
    num ++;
  }

}

void liteMyHanger(int n,int r,int g,int b){
  int ratio = 9;
  if( n != 0 ){
    pixels.setPixelColor( interval*(n-1)+1, pixels.Color(r*ratio,g*ratio,b*ratio));
    pixels.show();
  }else{
    for( int i = 0; i < numpixels; i++ ){
      pixels.setPixelColor( interval*(i-1)+1, pixels.Color(r*ratio,g*ratio,b*ratio));
      pixels.show();
    }
  }
}

void roulette(){
  int ratio = 9;
  int imax = random(20,30);
  for(int i=0; i<imax; i++ ){
    r = random(1,7);
    g = random(1,7);
    b = random(1,7);
    liteMyHanger(0,0,0,0);
    if( i%numpixels != 0 ){
      liteMyHanger(i%numpixels,r,g,b);
    }else{
      liteMyHanger(1,r,g,b);
    }
    delay(200);
  }

}
