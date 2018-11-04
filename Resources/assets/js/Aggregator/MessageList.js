import React from 'react';
import Message from './Message';
import uuidv4 from 'uuid/v4';

export default function MessageList(props) {
    const { messages } = props;
    if(messages.length === 0)
    {
        return <h2>No messages</h2>;
    }
    console.log(messages);
    return (
        <ul>
            {messages.map((message) => (
                <Message
                    message={message}
                    key={uuidv4()}
                />
                ))};
        </ul>

    )
}