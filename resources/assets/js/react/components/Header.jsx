import React from "react";
import {Link, NavLink} from "react-router-dom";
import trans from "../base/translate";
import user$ from "../base/stores/user$";

class Header extends React.Component {
    constructor(props) {
        super(props);

        this.state= {
            isUserDropdownOpen: false,
            isNavigationVisible: false
        };
    }

    toggleUserDropdown = (event) => {
      this.setState((prevState, props) => {
          return Object.assign(prevState, {isUserDropdownOpen: !prevState.isUserDropdownOpen})
      })
    };

    toggleNavigation = (event) => {
        this.setState((prevState, props) => {
            return Object.assign(prevState, {isNavigationVisible: !prevState.isNavigationVisible});
        });
    };

    render() {
        let isLoggedIn = localStorage.getItem('token');

        let landingPageLink = !isLoggedIn ? (
            <li className="nav-item">
                <NavLink exact to="/" className="nav-link">
                    <i className="fa fa-home"></i>
                    {trans('navigation.landingPage')}
                </NavLink>
            </li>
        ) : '';

        let registrationLink = !isLoggedIn ? (
            <li className="nav-item"><NavLink exact to="/register" className="nav-link"><i className="fa fa-user-plus" aria-hidden="true"></i> {trans('navigation.register')}</NavLink></li>
        ) : '';

        let feedLink = '';
        let searchLink = '';
        let logoutLink = '';
        let feedHistoryLink = '';
        let userDropdown = '';

        if (isLoggedIn) {
            let showUserDropdownMenuClass = this.state.isUserDropdownOpen ? ' show': '';
            let user = {};

            user$.subscribe((state) => {
                user = state;
            });

            feedLink = <li className="nav-item"><NavLink exact to="/feed" className="nav-link"><i className="fa fa-rss" aria-hidden="true"></i> {trans('navigation.feed')}</NavLink></li>;
            searchLink = <li className="nav-item"><NavLink exact to="/search" className="nav-link"><i className="fa fa-search" aria-hidden="true"></i> {trans('navigation.search')}</NavLink></li>;
            logoutLink = <li className="nav-item"><NavLink exact to="/logout" className="nav-link"><i className="fa fa-sign-out"></i> {trans('navigation.logout')}</NavLink></li>;
            feedHistoryLink = <li className="nav-item"><NavLink exact to="/feed/history" className="nav-link"><i className="fa fa-history" aria-hidden="true"></i> {trans('navigation.feedHistory')}</NavLink></li>;
            userDropdown = (
                <li className={`nav-item dropdown${showUserDropdownMenuClass}`}>
                    <a className="nav-link dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onClick={this.toggleUserDropdown}>
                        <i className="fa fa-user" aria-hidden="true"></i>&nbsp;
                        {user.name}
                    </a>
                    <div className={`dropdown-menu dropdown-menu-right${showUserDropdownMenuClass}`} aria-labelledby="userDropdown">
                        <h6 className="dropdown-header">{trans('navigation.manage')}</h6>
                        <NavLink to="/feed/manage" className="dropdown-item" onClick={this.toggleUserDropdown}>
                            <i className="fa fa-rss"></i>&nbsp;
                            {trans('navigation.feed')}
                        </NavLink>
                        <NavLink to="/categories" className="dropdown-item" onClick={this.toggleUserDropdown}>
                            <i className="fa fa-folder"></i>&nbsp;
                            {trans('navigation.categories')}
                        </NavLink>
                    </div>
                </li>
            );
        }

        let navigationClasses = `collapse navbar-collapse${this.state.isNavigationVisible ? ' show' : ''}`;

        return <nav className="navbar navbar-expand-lg navbar-dark bg-primary z-m-b-25">
            <div className="container">
                <Link to="/" className="navbar-brand">
                    <img src="https://crystal-rss.rocks/img/logo_small.png" height="30" className="d-inline-block align-top mr-1"/>
                    {trans('common.appName')}
                </Link>

                <button
                    className="navbar-toggler"
                    type="button"
                    data-toggle="collapse"
                    data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                    onClick={this.toggleNavigation}
                >
                    <span className="navbar-toggler-icon"></span>
                </button>

                <div className={navigationClasses} id="navbarSupportedContent">
                    <ul className="navbar-nav mr-auto">
                        {landingPageLink}
                        {feedLink}
                        {searchLink}
                    </ul>

                    <ul className="navbar-nav">
                        {feedHistoryLink}
                        {userDropdown}
                        {logoutLink}
                        {registrationLink}
                    </ul>
                </div>
            </div>
        </nav>;
    }
}

export default Header;