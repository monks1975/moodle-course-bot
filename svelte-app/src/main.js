import App from './App.svelte';

const blockDataElement = document.getElementById('block-data');
const { courseId, token } = JSON.parse(blockDataElement.textContent);

const app = new App({
  target: document.getElementById('svelte-root'),
  props: {
    courseId,
    accessToken: token,
  },
});

export default app;
