Use this plugin for sending build status notifications via Chatwork.

# Parameters:

- `room_id` - id of room chat
- `token` - chatwork token. Use **Drone Secret** . More detail at http://readme.drone.io/usage/secrets/
- `format` - let format message

# Example

The following is a sample configuration in your .drone.yml file:

```YML
notify:
  chatwork:
    image: fdplugins/chatwork
    room_id: room chat id
    token: $$CHATWORK_TOKEN
    format: "[info][title]{repo.owner}/{repo.name}#{build.commit} {build.status}[/title]Branch: {build.branch}\r\nAuthor: {build.author}\r\nMessage: {build.message}\r\n{system.link_url}/{repo.full_name}/{build.number}[/info]"
```