import 'package:demo_http/HomeWeatherScreen.dart';
import 'package:demo_http/WeatherScreen.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

void main() {
  runApp(const MainApp());
}

class MainApp extends StatelessWidget {
  const MainApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(home: Homeweatherscreen());
  }
}
