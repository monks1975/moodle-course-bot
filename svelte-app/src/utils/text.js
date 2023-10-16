// ~/svelte-app/src/utils/text.js

export class TextProcessor {
  constructor(text) {
    this.text = text;
  }

  stripHTML() {
    const tempEl = document.createElement('div');
    tempEl.innerHTML = this.text;
    this.text = tempEl.textContent || '';
    return this;
  }

  normalizeSpacing() {
    this.text = this.text.replace(/\s+/g, ' ').trim();
    return this;
  }

  normalizeCase() {
    this.text = this.text.toLowerCase();
    return this;
  }

  trim() {
    this.text = this.text.trim();
    return this;
  }

  value() {
    return this.text;
  }
}

// Usage
const processor = new TextProcessor(
  '<div>  Some text  <b>with</b> <i>HTML</i>   </div>'
);
const result = processor
  .stripHTML()
  .normalizeSpacing()
  .normalizeCase()
  .trim()
  .value();
console.log(result); // Output: "some text with html"
