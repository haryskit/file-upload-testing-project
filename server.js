const express = require('express');
const axios = require('axios');

const app = express();
const port = 3000;

const cloudName = 'dlbzf1uua'; // Your Cloudinary cloud name
const apiKey = '181869249849868'; // Your Cloudinary API Key
const apiSecret = '49IXALwuA7owTsVQIfSYXUYkjWs'; // Your Cloudinary API Secret
const cloudinaryApiUrl = `https://api.cloudinary.com/v1_1/${cloudName}/resources/image`;

app.get('/fetch-files', async (req, res) => {
  try {
    // Make a request to Cloudinary API from the server side
    const response = await axios.get(cloudinaryApiUrl, {
      auth: {
        username: apiKey,
        password: apiSecret,
      }
    });
    res.json(response.data); // Send the response back to the client
  } catch (error) {
    console.error(error);
    res.status(500).send('Failed to fetch files');
  }
});

app.listen(port, () => {
  console.log(`Server running on http://localhost:${port}`);
});
