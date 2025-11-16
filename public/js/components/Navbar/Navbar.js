const res = await fetch(new URL('Navbar.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    setup() {
        return {
            message: 'Hello World'
        }   
    }
}