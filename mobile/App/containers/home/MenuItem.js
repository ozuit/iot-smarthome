import React, { Component } from "react";
import { TouchableOpacity, Text } from "react-native";
import * as screenNames from "../../navigation/screen_names";
import { clearToken } from '../../utils/storage';
import { NavigationActions, StackActions } from 'react-navigation';

import Styles from "./Styles";

export default class MenuItem extends Component
{
    constructor(props) {
        super(props);
    }

    logout = () => {
        clearToken()
        const { navigation } = this.props.parent;
        const resetAction = StackActions.reset({
            index: 0,
            actions: [
                NavigationActions.navigate({ routeName: screenNames.LOGIN }),
            ],
        });
        navigation.dispatch(resetAction);
    }

    handleClickMenuItem = () => {
        if (this.props.menuScreen == 'LOGOUT') {
            this.logout()
        } else {
            this.props.parent.navigation.navigate(screenNames[this.props.menuScreen])
        }
    }

    render() {
        return (
            <TouchableOpacity
                style={Styles.menuItem}
                activeOpacity={0.8}
                onPress={this.handleClickMenuItem}
            >
                {this.props.children}
                <Text style={Styles.txtMenu}>{this.props.menuName}</Text>
            </TouchableOpacity>
        )
    }
}