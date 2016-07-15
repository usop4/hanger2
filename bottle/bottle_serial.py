# -*- coding: utf-8 -*-

import sys
from bottle import response,route,run

import os
import time

import serial

try:
    ser = serial.Serial()
    ser.baudrate = 9600
    for file in os.listdir('/dev'):
        if "tty.usbmodem" in file:
            ser.port = '/dev/'+file
            ser.open()
            time.sleep(0.1)
except:
    print sys.exc_info()[0]
    raise

@route('/arduino/<command>')
def arduino(command):
    response.set_header('Access-Control-Allow-Origin','*')
    try:
        ser.write(command)
        time.sleep(0.1)
        line = ser.readline()
        return line
    except:
        print sys.exc_info()[0]
    return command

@route('/test')
def test():
    response.set_header('Access-Control-Allow-Origin','*')
    try:
        line = ser.readline()
        return line
    except:
        print sys.exc_info()[0]
    return command

run(host="127.0.0.1",port=8946,debug=True)
