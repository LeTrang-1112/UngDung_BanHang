import 'package:demo_http/WeatherData.dart';
import 'package:demo_http/FetchWeather.dart';
import 'package:demo_http/WeatherScreen.dart';
import 'package:flutter/material.dart';

class Homeweatherscreen extends StatefulWidget {
  @override
  _HomeweatherscreenState createState() => _HomeweatherscreenState();
}

class _HomeweatherscreenState extends State<Homeweatherscreen> {
  late Future<WeatherData> futureWeather;
  final TextEditingController a = TextEditingController();

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
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            TextFormField(
              decoration: InputDecoration(labelText: "Enter City Name"),
            ),
            SizedBox(height: 16),
            ElevatedButton(
              onPressed: () async {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => WeatherScreen()),
                );
              },
              style: ElevatedButton.styleFrom(
                padding: EdgeInsets.symmetric(vertical: 16),
                backgroundColor: Colors.teal,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              child: Text(
                "Get Device Location",
                style: TextStyle(fontSize: 18, color: Colors.white),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
