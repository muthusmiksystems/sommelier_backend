import React, { Component } from "react";

import { Navigate } from "react-router-dom";

class NotFound extends Component {
    render() {
        return <Navigate to="/" />;
    }
}

export default NotFound;
