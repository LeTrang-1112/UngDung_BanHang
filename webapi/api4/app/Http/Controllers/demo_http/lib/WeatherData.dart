class WeatherData {
  final String description;
  final double temperature;
  final int humidity;
  final double windSpeed;
  final String name;
  WeatherData({
    required this.description,
    required this.temperature,
    required this.humidity,
    required this.windSpeed,
    required this.name,
  });

  factory WeatherData.fromJson(Map<String, dynamic> json) {
    return WeatherData(
      description: json['weather'][0]['description'],
      temperature: json['main']['temp'],
      humidity: json['main']['humidity'],
      windSpeed: json['wind']['speed'],
      name: json['name'],
    );
  }
}
