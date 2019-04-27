//
// Custom Shell [ Pierre & Andrej ]
//

#ifndef NDS_SHELLCOMMAND_H
#define NDS_SHELLCOMMAND_H

#include "header.hpp"

class ShellCommand {

private:
    String name;
    Command implementation;

public:
    ShellCommand(const String& name, Command function) {
        // interesting. https://en.cppreference.com/w/cpp/utility/move
        this->name = name;
        this->implementation = std::move(function);
    }

    bool operator() () {
        implementation(nullptr);
    }

    bool operator() (void * arg) {
        implementation(arg);
    }

    String getName() {
        return this->name;
    }

};

#endif //NDS_SHELLCOMMAND_H
