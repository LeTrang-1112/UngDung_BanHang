import 'package:demo_http/WeatherData.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

Future<WeatherData> fetchWeather(String cityName) async {
  final url =
      'https://api.openweathermap.org/data/2.5/weather?q=Ho%20Chi%20Minh&appid=a05b9739ca3010d2e99bfff725cee7de';

  final response = await http.get(Uri.parse(url));

  if (response.statusCode == 200) {
    final json = jsonDecode(response.body) as Map<String, dynamic>;
    return WeatherData.fromJson(json);
  } else {
    throw Exception('Lỗi Không thể tải dữ liệu ');
  }
}
