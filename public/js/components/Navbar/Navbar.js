const res = await fetch(new URL('Navbar.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    props: {
        nickname: {
            type: String,
            required: true
        }
    },
    emits: ['logout'],
    setup(props, { emit }) {
        return {
            emit,
        }   
    }
}