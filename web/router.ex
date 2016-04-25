defmodule Pickems.Router do
  use Pickems.Web, :router

  pipeline :api do
    plug :accepts, ["json", "json-api"]
  end

  scope "/api", Pickems do
    pipe_through :api

    resources "session", SessionController, only: [:index]
  end
end
