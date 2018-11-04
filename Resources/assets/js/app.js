import '../scss/app.scss';
import React from 'react';
import { render } from 'react-dom';
import AggregatorApp from './Aggregator/AggregatorApp';

render(
    <AggregatorApp />,
    document.getElementById('aggregator-app')
);


