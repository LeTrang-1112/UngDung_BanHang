import 'package:demo_http/WeatherData.dart';
import 'package:demo_http/FetchWeather.dart';
import 'package:flutter/material.dart';

class WeatherScreen extends StatefulWidget {
  @override
  _WeatherScreenState createState() => _WeatherScreenState();
}

class _WeatherScreenState extends State<WeatherScreen> {
  late Future<WeatherData> futureWeather;

  @override
  void initState() {
    super.initState();
    futureWeather = fetchWeather('Ho Chi Minh');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          "Weather App",
          style: TextStyle(color: Colors.blue),
        ),
        backgroundColor: Colors.white,
      ),
      body: Center(
        child: FutureBuilder<WeatherData>(
          future: futureWeather,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return CircularProgressIndicator();
            } else if (snapshot.hasError) {
              return Text('Error: ${snapshot.error}');
            } else if (!snapshot.hasData) {
              return Text('No data available');
            } else {
              final weather = snapshot.data!;
              return Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Text(
                    "🌦️",
                    style: TextStyle(fontSize: 200),
                  ),
                  Text(
                    ' ${(weather.temperature - 273.15).round()}°C',
                    style: TextStyle(fontSize: 60, color: Colors.black),
                  ),
                  Text(
                    '${weather.description}',
                    style: TextStyle(fontSize: 25, color: Colors.black),
                  ),
                  Text(
                    "${weather.name}",
                    style: TextStyle(fontSize: 25, color: Colors.black),
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        '💧 ${weather.humidity}%',
                        style: TextStyle(fontSize: 18, color: Colors.black),
                      ),
                      SizedBox(
                        width: 10,
                      ),
                      Text(
                        '🌪️ ${weather.windSpeed} m/s',
                        style: TextStyle(fontSize: 18, color: Colors.black),
                      ),
                    ],
                  )
                ],
              );
            }
          },
        ),
      ),
    );
  }
}
