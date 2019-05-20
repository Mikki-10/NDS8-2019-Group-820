///
/// Custom Shell: the JumpShell
/// Made by Andrej, Pierre and Sebastian
///

///
/// This header class file contains implementation! do not look for cpp file.
///

#ifndef NDS_SHELLCOMMAND_H
#define NDS_SHELLCOMMAND_H

#include "../header.hpp"
#include <functional>

/// accepts words (vector of strings); returns bool, whether the shell should continue
typedef std::function<bool(StringVec)> Command;

enum CommandType {
    BASIC = 0,
    JUMP = 1
};

class ShellCommand {

private:
    String m_name;
    String m_description;
    Command m_implementation;
    CommandType m_type;

public:
    ShellCommand(const String& name, CommandType type, const String& description, Command function) {
        /// interesting. https://en.cppreference.com/w/cpp/utility/move
        m_name = name;
        m_type = type;
        m_description = description;
        m_implementation = std::move(function);
    }

    bool operator() () {
        return m_implementation(StringVec());
    }

    bool operator() (StringVec& parameter) {
        return m_implementation(parameter);
    }

    CommandType getType() {
        return m_type;
    }

    String getName() {
        return m_name;
    }

    String getDesc() {
        return m_description;
    }

};

#endif //NDS_SHELLCOMMAND_H
