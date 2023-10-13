// ~/frontend/src/utils/moodle.js

const protocol = import.meta.env.VITE_MOODLE_PROTOCOL;
const baseUrl = import.meta.env.VITE_MOODLE_BASEURL;
const port = import.meta.env.VITE_MOODLE_PORT;

export async function moodleClient(
  endpoint,
  accessToken,
  params = {},
  customParams = {}
) {
  const url = `${protocol}://${baseUrl}:${port}/webservice/rest/server.php`;

  const transformedCustomParams = transformToMoodleCriteria(customParams);
  const criteria = { ...params, ...transformedCustomParams };

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        wstoken: accessToken,
        wsfunction: endpoint,
        moodlewsrestformat: 'json',
        ...criteria,
      }),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    return await response.json();
  } catch (error) {
    console.error('Fetch failed:', error);
    throw error;
  }
}

function transformToMoodleCriteria(obj) {
  const criteria = {};
  let index = 0;
  for (const [key, value] of Object.entries(obj)) {
    criteria[`criteria[${index}][key]`] = key;
    criteria[`criteria[${index}][value]`] = value;
    index++;
  }
  return criteria;
}
