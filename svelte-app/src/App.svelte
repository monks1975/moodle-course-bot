<!-- ~/svelte-app/src/App.svelte -->

<script>
  import { onMount } from 'svelte';
  import { moodleClient } from './lib/moodle.js';
  import { TextProcessor } from './utils/text.js';

  export let courseId;
  export let accessToken;

  let counter = 0;
  let userInfo = {};
  let courseContent = [];

  function increment() {
    counter = counter + 1;
  }

  /**
   * Extracts relevant module information (name and processed description) from an array of sections.
   * Utilizes TextProcessor to clean and normalize the module description.
   */
  const extractMods = (sections) => {
    return sections
      .map((section) => {
        return section.modules.map((module) => {
          let desc = null;
          if (module.description) {
            const processor = new TextProcessor(module.description);
            desc = processor.stripHTML().normalizeSpacing().trim().value();
          }

          return {
            id: module?.id ?? null,
            section: section?.section ?? null,
            name: module?.name ?? null,
            description: desc,
            instance: module?.instance ?? null,
            contextId: module?.contextid ?? null,
            modName: module?.modname ?? null,
          };
        });
      })
      .flat();
  };

  onMount(async () => {
    console.log('The Svelte component has mounted.');

    if (accessToken) {
      try {
        const userFilter = { id: 2 };
        const userInfo = await moodleClient(
          'core_user_get_users',
          accessToken,
          null,
          userFilter // transformed into special params
        );
        console.log(userInfo);

        const courseFilter = { courseid: 2 };
        const course = await moodleClient(
          'core_course_get_contents',
          accessToken,
          courseFilter // not transformed into special params
        );
        courseContent = extractMods(course);
        console.log(courseContent);
      } catch (error) {
        console.error('There was a problem:', error);
      }
    }
  });
</script>

<div class="oai-container">
  {#if courseId}
    <p>Course ID is: {courseId}</p>
  {/if}

  {#if userInfo && userInfo.users && userInfo.users.length}
    <p>User Full Name: {userInfo.users[0].fullname}</p>
    <p>User Email: {userInfo.users[0].email}</p>
  {:else}
    <p>User data not available...</p>
  {/if}

  <div class="oai-flex-center">
    <button class="oai-button" on:click={increment}>Increment</button>
    <div>{counter}</div>
  </div>
</div>

<style>
  button.oai-button {
    border-radius: 4px;
    border: 1px solid transparent;
    padding: 0.6em 6em;
    font-size: 0.9em;
    font-weight: 500;
    font-family: inherit;
    color: white;
    flex-grow: 0;
    background-color: chocolate;
    cursor: pointer;
    transition: border-color 0.25s;
  }
  button.oai-button:hover {
    border-color: #646cff;
  }
  button.oai-button:focus,
  button.oai-button:focus-visible {
    outline: 4px auto -webkit-focus-ring-color;
  }

  .oai-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .oai-flex-center {
    display: flex;
    align-items: center !important;
    gap: 8px;
  }
</style>
