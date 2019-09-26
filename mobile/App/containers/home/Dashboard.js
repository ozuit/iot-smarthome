import React, { Component } from 'react';
import { View, ScrollView, StatusBar } from 'react-native';
import { connect } from 'react-redux';
import Styles from "./Styles";
import MenuItem from "./MenuItem";
import menus from './config';

class HomeContainer extends Component {
    render() {
        const { userInfo } = this.props.data

        return (
            <ScrollView>
                <StatusBar backgroundColor="#c60b1e" barStyle="light-content" />
                <View style={Styles.container}>
                    {
                        menus.map((item, index) => (
                            item.roles.indexOf(userInfo.role) != -1 ?
                                <MenuItem key={index} parent={this.props} menuName={item.name} menuScreen={item.screen}>
                                { item.icon }
                                </MenuItem>
                            : null
                        ))
                    }
                </View>
            </ScrollView>
        );
    }
}

const mapStateToProps = (state) => ({
    data: {
        userInfo: state.login.userInfo
    }
})

export default connect(mapStateToProps, null)(HomeContainer)