const apiKey = '12b804b396326a7144c45c58f7a34539';
const elements = {
  cityName: document.querySelector('.city'),
  temperature: document.querySelector('.temp'),
  humidity: document.querySelector('.humidity'),
  pressure: document.querySelector('.pressure'),
  wind: document.querySelector('.wind'),
  time: document.querySelector('.time'),
  description: document.querySelector('.description'),
  searchButton: document.querySelector('button'),
  cityInput: document.querySelector('#city-input'),
  weatherIcon: document.querySelector('.weather-icon'),
};

function getWeatherData(city) {
  const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&appid=${apiKey}`;
  fetch(apiUrl)
    .then(response => response.json())
    .then(data => {
      const { name, main, wind, timezone, weather } = data;
      elements.cityName.textContent = name;
      elements.temperature.textContent = Math.round(main.temp) + '°C';
      elements.humidity.textContent = main.humidity + '%';
      elements.pressure.textContent = main.pressure + ' hpa';
      elements.wind.textContent = Math.round(wind.speed) + ' km/h';
      const cityTime = new Date((new Date().getTime()) + ((data.timezone + new Date().getTimezoneOffset() * 60) * 1000));
      elements.time.textContent = cityTime.toLocaleTimeString();
      elements.description.textContent = weather[0].description;
      const icon = elements.weatherIcon;
      if (data.weather[0].main == "Clouds") {
        icon.src = "clouds.png";
    } else if (data.weather[0].main == "Clear") {
        icon.src = "clear-sky.png";
    } else if (data.weather[0].main == "Rain") {
        icon.src = "rain.png";
    } else if (data.weather[0].main == "Drizzle") {
        icon.src = "drizzling.png";
    } else if (data.weather[0].main == "Mist") {
        icon.src = "mist.png";
    } else if (data.weather[0].main == "Snow") {
        icon.src = "christmas-tree.png";
    }

      elements.weatherIcon.src = icon.src;
    })
    .catch(error => console.log(error));
}

elements.searchButton.addEventListener('click', event => {
  event.preventDefault();
  const city = elements.cityInput.value.trim();
  if (city !== '') {
    getWeatherData(city);
  }
});

getWeatherData('kodiak');