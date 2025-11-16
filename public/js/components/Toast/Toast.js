const res = await fetch(new URL('Toast.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    props: {
        message: {
            type: String,
            required: true
        },
        type: {
            type: String,
            required: true
        }
    },
    setup(props, { emit }) {
        const typeClass = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500',
        }
        return {
            typeClass,
        }
    }
}